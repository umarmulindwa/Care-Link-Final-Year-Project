<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends BaseController
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            // return response()->json($request->all());

            $user = User::where('email', $request->email)->first();
            // return $user;
            if (is_null($user)) {
                return apiErrorResponse('Wrong email or password');
            }
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
                // return apiErrorResponse("The provided credentials are incorrect.");
            }

            $apiToken = $user->createToken("api-personel-token")->plainTextToken;

            $user->api_token = $apiToken;
            $user->token = $apiToken;
            $user->save();

            return apiResponse(array('user' => $user, "token" => $apiToken, 'access_token' => $apiToken));
        } catch (\Throwable $th) {
            return apiErrorResponse($th->getMessage(), ['trace' => $th->getTrace()]);
        }
    }

    public function getUser()
    {
        return apiResponse(["user" => request()->user()]);
    }

    public function loginOut(Request $request)
    {
        try {
            $user = $request->user();
            // Revoke all tokens...
            $user->tokens()->delete();

            // Revoke a specific token...
            // $user->tokens()->where('id', $tokenId)->delete();
            return apiResponse("Log out Successfully");
        } catch (\Throwable $th) {
            //throw $th;
            return apiErrorResponse($th->getMessage());
        }
    }
}
