<?php

namespace App\Http\Controllers;

use App\Replay;
use Cache;
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
            return Replay::count();
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
                    'version' => preg_replace('/^v/', '', $release->tag_name)
                ];
            } catch (Exception $e) {
                Log::warning("Error getting setup link: $e");
                return ['url' => 'https://github.com/poma/Hotsapi.Uploader/releases/latest', 'version' => null];
            }
        });

        return view("upload", ['setupLink' => $data['url'], 'setupVersion' => $data['version']]);
    }

    public function docs()
    {
        return view("docs");
    }

    public function faq()
    {
        return view("faq");
    }
}
