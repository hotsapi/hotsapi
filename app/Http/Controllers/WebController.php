<?php

namespace App\Http\Controllers;

use App\Replay;
use Cache;
use DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Log;
use View;

class WebController extends Controller
{

    public function __construct()
    {
        $totalReplayCount = Cache::remember('totalReplayCount', 1, function () {
            try {
                // return Replay::count(); // too slow, let's use an approximation instead
                return DB::select("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'replays'")[0]->TABLE_ROWS;
            } catch (Exception $e) {
                Log::error("Error getting replay count: $e");
                return '???';
            }
        });
        View::share('totalReplayCount', $totalReplayCount);
    }

    public function home()
    {
        return view("home");
    }

    public function upload()
    {
        $data = Cache::remember('setupLink', 60, function () {
            try {
                $release = json_decode((new Client())->get('https://api.github.com/repos/poma/Hotsapi.Uploader/releases/latest')->getBody());
                return [
                    'url' => collect($release->assets)->where('name', 'HotsApiUploaderSetup.exe')->first()->browser_download_url,
                    'zip' => collect($release->assets)->where('name', 'HotsApi.zip')->first()->browser_download_url,
                    'version' => preg_replace('/^v/', '', $release->tag_name)
                ];
            } catch (Exception $e) {
                Log::warning("Error getting setup link: $e");
                return ['url' => 'https://github.com/poma/Hotsapi.Uploader/releases/latest', 'zip' => 'https://github.com/poma/Hotsapi.Uploader/releases/latest', 'version' => null];
            }
        });

        return view("upload", ['setupLink' => $data['url'], 'zipLink' => $data['zip'], 'setupVersion' => $data['version']]);
    }

    public function docs()
    {
        return view("docs");
    }

    public function swagger()
    {
        return view("swagger");
    }

    public function faq()
    {
        return view("faq");
    }
}
