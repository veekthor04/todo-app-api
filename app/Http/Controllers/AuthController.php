<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    //
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return response([
                'message'=>'An error occurred while attempting to create a new user',
                'error'=>$validator->errors()
            ], 500);
        }else{
            $user = User::create([
                'name'=>$request['name'],
                'email'=>$request['email'],
                'password'=>bcrypt($request['password'])
            ]);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response(['message'=>'Logged out']);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email'=>'required|string',
            'password'=>'required|string|'
        ]);

        if ($validator->fails()) {
            return response([
                'message'=>'An error occurred while attempting to login',
                'error'=>$validator->errors()
            ], 500);
        }
        $user = User::where('email', $request['email'])->first();

        if(!$user || !Hash::check($request['password'], $user->password)){
            return response(['message'=>'Invalid credentials'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
