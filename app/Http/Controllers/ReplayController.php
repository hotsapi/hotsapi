<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReplayResource;
use App\Replay;
use App\Services\HotslogsUploader;
use App\Services\ParserService;
use App\Services\ReplayService;
use Illuminate\Http\Request;

class ReplayController extends Controller
{
    // Number of replays per page
    const PAGE_SIZE = 1000;
    const PAGE_SIZE_WITH_PLAYERS = 100;

    /**
     * Upload a replay
     *
     * @param Request $request
     * @param ReplayService $replayService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ReplayService $replayService)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['success' => false, 'Error' => 'no file specified']);
        }

        $result = $replayService->store($request->file('file'), $request->uploadToHotslogs);

        $response = ['success' => true, 'status' => $result->status, 'originalName' => $request->file('file')->getClientOriginalName()];
        if (isset($result->replay)) {
            $response += [
                'filename' => $result->replay->filename,
                'url' => $result->replay->url,
                'id' => $result->replay->id
            ];
        }
        return response()->json($response);
    }

    /**
     * Show replay list
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $query = Replay::on('mysql_slave')->select(\DB::raw('/*+ MAX_EXECUTION_TIME(30000) */ *'))->with('game_map');

        if ($request->min_id) {
            $query->where('id', '>=', $request->min_id);
        }

        if ($request->existing) {
            $query->where('deleted', 0);
        }

        if ($request->with_players) {
            $query->with('bans', 'bans.hero', 'players', 'players.hero', 'players.talents', 'players.score');
        }

        $query->orderBy('id');
        $query->limit($request->with_players ? ReplayController::PAGE_SIZE_WITH_PLAYERS : ReplayController::PAGE_SIZE);
        $replays = $query->get();
        return ReplayResource::collection($replays);
    }

    /**
     * Show parsed replay list
     *
     * @param Request $request
     * @return string
     */
    public function parsed(Request $request)
    {
        $query = Replay::on('mysql_slave')->select(\DB::raw('/*+ MAX_EXECUTION_TIME(30000) */ *'))->with('game_map');

        if ($request->min_parsed_id) {
            $query->where('parsed_id', '>=', $request->min_parsed_id);
        }

        if ($request->with_players) {
            $query->with('bans', 'bans.hero', 'players', 'players.hero', 'players.talents', 'players.score');
        }

        $query->orderBy('parsed_id');
        $query->limit($request->with_players ? ReplayController::PAGE_SIZE_WITH_PLAYERS : ReplayController::PAGE_SIZE);
        $replays = $query->get();
        return ReplayResource::collection($replays);
    }

    /**
     * Show replay details
     *
     * @param $replay
     * @return string
     */
    public function show($replay)
    {
        $replay = Replay::on('mysql_slave')->with('game_map', 'bans', 'bans.hero', 'players', 'players.hero', 'players.talents', 'players.score')->findOrFail($replay);
        return new ReplayResource($replay);
    }

    /**
     * Check whether a replay with given fingerprint is already uploaded
     * Compatible with HotsLogs
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkV3(Request $request)
    {
        $replay = Replay::where('fingerprint', $request->fingerprint)->first();
        if ($replay != null && $request->uploadToHotslogs) {
            HotslogsUploader::queueForUpload($replay);
        }
        return response()->json(['exists' => $replay != null]);
    }

    /**
     * Check whether a replay with given fingerprint is already uploaded
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkV2(Request $request)
    {
        $fingerprint = preg_replace('/^(\w+)-(\w+)-(\w{2})(\w{2})-/', '$1-$2-$4$3-', $request->fingerprint); // swap 2 bytes
        $replay = Replay::where('fingerprint', $fingerprint)->first();
        if ($replay != null && $request->uploadToHotslogs) {
            HotslogsUploader::queueForUpload($replay);
        }
        return response()->json(['exists' => $replay != null]);
    }

    /**
     * Check whether replays with given fingerprints are already uploaded
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function massCheck(Request $request)
    {
        $all = preg_split("/\r\n|\n|\r/", $request->getContent());
        $exists = Replay::select('fingerprint')->whereIn('fingerprint', $all)->get()->map->fingerprint->toArray();
        $absent = array_values(array_diff($all, $exists));

        return response()->json(['exists' => $exists, 'absent' => $absent]);
    }

    /**
     * Get the earliest replay id for a given date
     *
     * @param Request $request
     * @return int
     */
    public function minId(Request $request)
    {
        return Replay::where('game_date', '>=', $request->date)->min('id');
    }

    /**
     * Get the earliest replay id for a given date
     *
     * @param Request $request
     * @return int
     */
    public function minParsedId(Request $request)
    {
        return Replay::where('game_date', '>=', $request->date)->min('parsed_id');
    }

    /**
     * Get minimum supported build
     *
     * @return int
     */
    public function minimumBuild()
    {
        return ParserService::MIN_SUPPORTED_BUILD;
    }
}
