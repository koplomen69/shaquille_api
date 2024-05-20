<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $client = new Client([
            'base_uri' => "http://127.0.0.1:8000/api/login",
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $res = $client->post('login', [
            'json' => $credentials,
            'http_errors' => false
        ]);

        // Dekode body respons
        $responseBody = json_decode($res->getBody(), true);

        // Periksa kode status dan tangani respons
        if ($res->getStatusCode() == 200 && isset($responseBody['token'])) {
            session(['api_token' => $responseBody['token']]);
            $request->session()->regenerate();
            return redirect('/home');
        } else {
            // Tangani otentikasi yang gagal
            return redirect()->back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        }
    }

}
