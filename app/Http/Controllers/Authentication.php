<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;

class Authentication extends Controller
{
    public function register(Request $request) {
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|unique:App\Models\User,username',
            'email' => 'required|string|regex:/^.+@.+$/i|unique:App\Models\User,email',
            'password' => 'required|string',
            'phone' => 'regex:/^[0-9]{8}$/',
            'dob' => 'required|date'
        ]);

        if($v->fails())
            return $v->errors();

        $user = User::create([
            'name' => $request['name'],
            'username' => $request['username'],
            'phone' => $request['phone'],
            'dob' => $request['dob'],
            'email' => $request['email'],
            'password' => bcrypt($request['password'])
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $v = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if($v->fails())
            return $v->errors();

        $user = User::where('email', $request['email'])->first();

        if(!$user || !Hash::check($request['password'], $user->password)) {
            return response([
                'message' => 'Incorect cred'
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        if($request->user())
            auth()->user()->currentAccessToken()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function forgot_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Password reset successfully'
            ]);
        }

        return response([
            'message'=> __($status)
        ], 500);
    }
}
