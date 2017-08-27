<?php

namespace App\Http\Controllers;

use App\Replay;
use App\Services\ParserService;
use App\Services\ReplayService;
use Illuminate\Http\Request;

class ReplayController extends Controller
{
    /**
     * Upload replay
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

        $result = $replayService->store($request->file('file'));

        $response = ['success' => true, 'status' => $result->status];
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
        $query = Replay::query();

        if ($request->start_date) {
            $query->where('game_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('game_date', '<=', $request->end_date);
        }

        if ($request->game_map) {
            $query->where('game_map', $request->game_map);
        }

        if ($request->game_type) {
            $query->where('game_type', $request->game_type);
        }

        if ($request->min_id) {
            $query->where('id', '>=', $request->min_id);
        }

        if ($request->player) {
            $query->whereHas('players', function ($query) use ($request) {
                $query->where('battletag', $request->player);
            });
        }

        return $query->get()->toJson();
    }

    /**
     * Show replay details
     *
     * @param Replay $replay
     * @return string
     */
    public function show(Replay $replay)
    {
        $replay->load('players');
        return $replay->toJson();
    }

    /**
     * Check whether a replay with given fingerprint is already uploaded
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $exists = Replay::where('fingerprint', $request->fingerprint)->exists();
        return response()->json(['exists' => $exists]);
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
        $absent = array_diff($all, $exists);

        return response()->json(['exists' => $exists, 'absent' => $absent]);
    }

    public function minimumBuild()
    {
        return ParserService::MIN_SUPPORTED_BUILD;
    }
}
