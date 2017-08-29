<?php

namespace App\Http\Controllers;

use App\Replay;
use Illuminate\Http\Request;

class WebController extends Controller
{

    public function __construct()
    {
        $totalReplayCount = \Cache::remember('totalReplayCount', 1, function () {
            return Replay::count();
        });
        \View::share('totalReplayCount', $totalReplayCount);
    }

    public function home()
    {
        return view("home");
    }

    public function upload()
    {
        return view("upload");
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
