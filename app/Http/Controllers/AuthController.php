<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tokens;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);

    } 

    public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message'=> 'Bad creds '
            ], 401 );
        }
        $token = Token::where('tokenable_id', $user['id']);
        if ($token) {

        }
        // $x = auth()->user();
        // if (auth()->user()->tokens()){
        //     $x = "new";
        //     auth()->user()->tokens()->delete();
        // }

        // $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            // 'x' => $x
        ];

        return response($response, 201);

    }

    public function logout(Request $request){

        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
}
