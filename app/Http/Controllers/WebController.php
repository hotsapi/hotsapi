<?php

namespace App\Http\Controllers;

use App\Replay;
use Cache;
use DB;
use Exception;
use Guzzle;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
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

    public function bnetAuth(Request $request)
    {
        $provider = new GenericProvider([
            'clientId'                => env('BNET_CLIENT_ID'),
            'clientSecret'            => env('BNET_SECRET'),
            'redirectUri'             => 'https://hotsapi.net/bnet-auth',
            'urlAuthorize'            => 'https://eu.battle.net/oauth/authorize',
            'urlAccessToken'          => 'https://eu.battle.net/oauth/token',
        ]);

        // If we don't have an authorization code then get one
        if (!$request->code) {
            $authorizationUrl = $provider->getAuthorizationUrl();
            // Get the state generated for you and store it to the session.
            session(['oauth2state' => $provider->getState()]);
            return redirect($authorizationUrl);
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (!$request->state || session('oauth2state') && $request->state !== session('oauth2state')) {
            $request->session()->forget('oauth2state');
            exit('Invalid state');
        } else {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', ['code' => $request->code]);

                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'https://eu.battle.net/oauth/userinfo',
                    $accessToken
                );

                $response = Guzzle::send($request);
                $data = json_decode($response->getBody());

                echo "Battletag is: " . $data->battletag . "\n";
                echo "Account id is: " . $data->id . "\n"; //$data->sub
            } catch (IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit('Error: ' . $e->getMessage());
            }

        }
    }
}
