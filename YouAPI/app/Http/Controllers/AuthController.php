<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;


class AuthController extends Controller
{

    public function register(Request $request){
        try {
            $user = $request->validate([
              'name'=> 'required',
              'password'=> 'required|min:6',
              'email'=> 'required|email|unique:users',
            ]);

            $hashedPwd = Hash::make($user['password']);

            $user = User::create([
              'name' => $user['name'],
              'email' => $user['email'],
              'password' => $hashedPwd,
              'role_id' => 1,

            ]);

            if($user){

              $wallet = Wallet::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'balance' => 0,
                'type' => 'standard',
              ]);

              $tokenResult = $user->createToken('Personal Access Token');
              $token = $tokenResult->plainTextToken;

              return response()->json([
                'message' => 'user created successfully',
                'uuid' => Uuid::fromBytes($wallet->id)->toString(),
                'wallet' => $wallet,
                'accessToken'=> $token,], 201);
            }

            else {
              return response()->json(['error'=>'error creating user'], 500);
            }
        }
        catch (\Exception $e) {
            return response()->json(['error'=>'error processing request: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = $request->only('email', 'password');

        if (auth()->attempt($user)) {
            $user = auth()->user();
            $tokenRes = $user->createToken('Personal Access Token');
            $token = $tokenRes->plainTextToken;

            return response()->json([
                'message' => 'login successful',
                'accessToken' => $token], 200);
        }
        else
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'message' => 'user logged out successfully']);
        } else
        {
            return response()->json([
                'error' => 'user not authenticated'], 401);
        }
    }

}
