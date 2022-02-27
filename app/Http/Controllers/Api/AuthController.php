<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){

        $data = $request->validate([
             'name'=>'required|string|max:255',
             'email'=>'required|email|unique:users,email',
             'password'=>'required|min:6'
         ]);

        $data['password'] = bcrypt($data['password']);

        $user =  User::create($data);

        $token = $user->createToken('lvapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request){
        
        $data = $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if(!$user || ! Hash::check($data['password'], $user->password)){
            return response([
                'message'=> 'Bad Creds'
            ], 401);
        }

        $token = $user->createToken('lvapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(){

        auth()->user()->tokens()->delete();

        return ['message'=>'Logout...'];
    }
}
