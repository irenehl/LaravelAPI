<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Http\Resources\AuthenticationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;

class Authentication extends Controller
{
    public function register(UserStoreRequest $request) {
        $validated = $request->validated();
        $user = User::create($validated);

        $token = $user->createToken('token')->plainTextToken;

        return new AuthenticationResource([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(LoginRequest $request) {
        $validated = $request->validate();

        $user = User::where('email', $validated['email'])->first();

        if(!$user || !Hash::check($request['password'], $user->password)) {
            return response([
                'message' => 'Incorect cred'
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return new AuthenticationResource([
            'user' => $user,
            'token' => $token
        ]);
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
        return [
            'status' => __($status)
        ];
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
