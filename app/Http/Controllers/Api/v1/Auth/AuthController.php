<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
     * register user
     */
    public function register(Request $request): JsonResponse
    {

        $base_url = env('APP_URL');

        $request->validate([
            'name' => 'required|max:25',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        //create new user
        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        $user->save();
        return Response()->json($user, HttpResponse::HTTP_CREATED);
    }


    /*
     * login user
     */
    public function login(Request $request): JsonResponse
    {

        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $login_detail = request(['email', 'password']);

        if (!Auth::attempt($login_detail)) {

            return response()->json(
                [
                    'error' => 'Login Failed. Please check your login detail'
                ], HttpResponse::HTTP_UNAUTHORIZED
            );
        }

        $user = $request->user();
        $hasToken = $user->tokens()->where('name',$request->get('email'))->first();

        if ($hasToken)
        {
            // Revoke all tokens...
            $user->tokens()->delete();
        }

        //create access token
        $hasToken = $user->createToken($request->get('email'));

        return response()->json([
            'access_token' => "Bearer " . $hasToken->plainTextToken,
            'token_id' => $hasToken->accessToken->tokenable_id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email

        ], HttpResponse::HTTP_OK);

    }
}
