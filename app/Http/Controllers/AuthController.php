<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    public function redirect(Request $request)
    {
        $result = bin2hex(random_bytes(20));
        $code_verifier = $result;
        $state = Str::random(40);
        $code_verifier = Str::random(128);
        $request->session()->put('state', $state = Str::random(40));
        $request->session()->put('code_verifier', $code_verifier = Str::random(128));

        $code = $request->query('code');
        // Redis::set($code, [$state, .$code_verifier]);


        // Cache::put('state', $state, $request->query('code'));
        // Cache::put('code_verifier', $code_verifier, $request->query('code'));

        // Log::info(session()->all());
        // Log::info(session()->getId());
        
        // dd($request->session()->get('state'));
        // session(['state' => $state = Str::random(40)]);
        // session(['code_verifier' => $code_verifier = Str::random(128)]);

        
        $encoded = base64_encode(hash('sha256', $code_verifier, true));
        $codeChallenge = strtr(rtrim($encoded, '='), '+/', '-_');
        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $code_verifier, true)),
            '='
        ), '+/', '-_');

        $query = http_build_query([
            'client_id' => '1',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'prompt' => 'login'
        ]);

        $request->session()->save();
        return redirect('http://127.0.0.1:8001/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {

        // $state = Cache::pull('state');
        // $code_verifier = Cache::pull('code_verifier');
        $code_verifier = $request->session()->get('code_verifier');
        $state = $request->session()->get('state');
        // Log::info(session()->all());
        // Log::info(session()->getId());
        // $request->session()->setId(json_decode($request->state, true)['session']);
        // dd($request->session()->all());
        // dd(DB::table('sessions')->select()->where('id', )->first());
        // Log::info(Session::all());
     
        // $codeVerifier =session('code_verifier');
        // dd($state, $request, $request->cookies);

        Log::info($request->state);
        Log::info($state);

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            dd($state, $request),
            InvalidArgumentException::class
        );

        $response = Http::asForm()->post('http://127.0.0.1:8001/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '1',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'code_verifier' => $code_verifier,
            'code' => $request->code,
        ]);

        return $response->json();
    }
}
