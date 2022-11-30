<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirect(Request $request)
    {

        $result = bin2hex(random_bytes(20));
        $code_verifier = $result;
        // $request->session()->put('state', $state = Str::random(40));
        // $request->session()->put('code_verifier', $code_verifier = Str::random(128));
        session(['state' => $state = Str::random(40)]);
        session(['code_verifier' => $code_verifier = Str::random(128)]);

        
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

        return redirect('http://127.0.0.1:8001/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {

        // $state = $request->session()->get('state');
        // $codeVerifier = $request->session()->get('code_verifier');
        $state = session('state');
        $codeVerifier =session('code_verifier');
        dd($state, $request);
        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            dd($state, $request),
            InvalidArgumentException::class
        );

        $response = Http::asForm()->post('http://127.0.0.1:8001/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '1',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'code_verifier' => $codeVerifier,
            'code' => $request->code,
        ]);

        return $response->json();
    }
}
