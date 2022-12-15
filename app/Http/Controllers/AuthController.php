<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

//Class Auth Controller contains all methods for authentication of the user using a OAuth2.0 server 
class AuthController extends Controller
{
    // The Redirect request is made to the Auth server passing in key params, (outlined below) 
    // This implementation is using PKCE, (proof key code exchange) and hence we don't need to pass the client secret
    public function redirect(Request $request)
    {
        // Create a randome hexidecimal value of 20 characters / digits, assign this to result which is then 
        // Assigned to code_verifier to verify the origin of the request
        $result = bin2hex(random_bytes(20));
        $code_verifier = $result;

        //Put the state and code verfier in the session so the request's origin can be verified on the auth server 
        $request->session()->put('state', $state = Str::random(40));
        $request->session()->put('code_verifier', $code_verifier = Str::random(128));
        Session::save();

        // A code challenge is created from the code verifier as an extra layer of authentication 
        // This is passed in to the redirect request but not needed for the callback
        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $code_verifier, true)),
            '='
        ), '+/', '-_');

        // The following query is sent through in the redirect request
        // The client id, redirect URI, response type are to be provided by the auth server  
        // Prompt is set to none by default, it can be set to login, (testing both doesn't show any difference)
        $query = http_build_query([
            'client_id' => '1',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'prompt' => ''
        ]);

        // The redirect is made to an endpoint, oauth/authorize passing in the params in to the query string 
        return redirect('http://auth-server.test/oauth/authorize?' . $query);
    }

    // The callback function is for handling redirecting the user back to the client app
    // When this is hit the user is provided an authorization code that can be exchanged for a token
    // The authorization code will only work if the code verifier and state params match the ones made in the redirect request
    public function callback(Request $request)
    {
        // To verify the origin of the request, the code verifier and the state are pulled from the session 
        $code_verifier = $request->session()->pull('code_verifier');
        $state = $request->session()->pull('state');

        // Handling the exception if the state is either null / empty (length of 0 characters) 
        // or doesn't match the values put in to the session 
        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );
        // Once the code verifier and state are matched, a post request is made to the 
        // oauth/token endpoint which is in-built in Laravel passport to get an access token
        $response = Http::asForm()->post('http://auth-server.test/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '1',
            'redirect_uri' => 'http://127.0.0.1:8080/callback',
            'code_verifier' => $code_verifier,
            'code' => $request->code,
        ]);
        // Parse the response of the access token and token type in to JSON
        // Assign these values to variables  
        $auth_grant = json_decode((string) $response->getBody(), true);
        $token_type = $auth_grant['token_type'];
        $access_token = $auth_grant['access_token'];

        // Pass the token type and access token in to the header of the GET request to get the logged in users's details
        // This get request is made to the auth server where the user has registered
        // Assign these values to a variable 
        $user_auth = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token_type . ' ' . $access_token,
        ])->get('http://auth-server.test/api/user');

        // Parse the previously declared variables to JSON and assign it to another variable
        $usrAuth = json_decode((string) $user_auth->getBody(), true);

        // Check if the user exists in the database, if they do, then use that user's details 
        // If not then create that user 
        if (User::where('email', '=', $usrAuth['email'])->exists()) {
            $user = User::where('email', $usrAuth['email'])->first();
        } else {
            $user = User::create([
                'name' => $usrAuth['name'],
                'email' => $usrAuth['email'],
            ]);
        }

        // Assign a Sacntum token to use 
        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'user' => $user,
            'token' => $token
        ];

        // Use the Auth namespace to login the user and redirect them to the dashboard
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    // Logout the user and redirect them to the login screen 
    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect('login');
    }
}
