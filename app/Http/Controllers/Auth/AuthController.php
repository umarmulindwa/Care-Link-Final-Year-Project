<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\OTPRequest;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;



class AuthController extends Controller
{
    public function returnFromModule(Request $request)
    {
        // $token = $request->header('Auth_token');
        // Session::setId($token);
        // Session::start();
        //Checks whether the user is authenticated(has a runing session) from the main application if not it redirects them back to login
        if (auth('web')->check()) {
            $user = auth()->user();
            return \Inertia\Inertia::render('dashboard', [
                'user' => $user
            ]);
        } else {
            return \Inertia\Inertia::render('login');
        }
    }

    public function loginAs(Request $request)
    {
        $user = User::where('email', $request->user)->first();

        $userApiToken = PersonalAccessToken::where('tokenable_id', $user->id)->first();
        if ($userApiToken == null) {
            // create a  token if the user does not have one already
            $createdToken = $user->createToken($user->name)->plainTextToken;
            $user->api_token =  $createdToken;
            $user->save();
        }

        //setting sessions
        Auth::guard('web')->login($user);
        Auth::guard('sanctum')->setUser($user);

        return to_route('dashboard',['impersonation' =>true]);
    }



    /**
     * @throws Exception
     */
    public function sendOTP(OTPRequest $request): void
    {
        if($request->find_user()){
            $request->sendOTP();
        }
    }


    /**
     * @throws ValidationException
     */
    public function confirmIdentityWithOtp(OTPRequest $request)
    {
        $request->authenticate();

        $user = $request->find_user();

        if(!$user) return null;

        // create a  token if the user does not have one already
        $token = $user->createToken($user->name)->plainTextToken;
        $user->api_token =  $token;
        $user->save();

        return $token;
    }
}
