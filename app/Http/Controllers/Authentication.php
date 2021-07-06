<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;
use Exception;

class Authentication extends Controller
{
    public function register(Request $request) {
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|unique:App\Models\UserModel,username',
            'email' => 'required|string|regex:/^.+@.+$/i|unique:App\Models\UserModel,email',
            'password' => 'required|string',
            'phone' => 'regex:/^[0-9]{8}$/',
            'dob' => 'required|date'
        ]);

        if($v->fails())
            return $v->errors();

        $user = UserModel::create([
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

        $user = UserModel::where('email', $request['email'])->first();

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
}
