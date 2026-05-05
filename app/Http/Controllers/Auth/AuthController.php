<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            //'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            // 'lName' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'weight' => 'numeric|min:20',
            'gender' => 'string',
        ]);
        // $weight = $request->weight;
        // $age = $request->age;
        // $height = $request->height;
        // if(!is_numeric($weight) || !is_numeric($age) || !is_numeric($height))
        // {
        //     die('Either input weight, age or height is not numeric!');
        // }
        $gender = strtolower($request->gender);
        if($gender != 'male' && $gender != 'female')
        {
            return response()->json([
                'gender' => 'The formula works with gender assigned at birth. Please enter male or female.'
            ], 422);
        }
        $email = $request->email;
        if(strpos($email, '.') === false)
        {
            return response()->json([
                'email' => 'Enter a valid email.'
            ], 422);
        }
        $user = new User;
        // $name = $request->fName . ' ' . $request->lName;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->weight = $request->weight;
        $user->weightType = $request->weightType;
        $user->gender = $request->gender;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function edit(Request $request)
    {
        $request->validate([
            'name' => 'string',
            'email' => 'string|email',
            'weight' => 'numeric|min:20',
            'gender' => 'string',
        ]);
        $gender = strtolower($request->gender);
        // $gender = $request->gender;
        if($gender != 'male' && $gender != 'female')
        {
            return response()->json([
                'gender' => 'The formula works with gender assigned at birth. Please enter male or female.'
            ], 422);
        }
        $email = $request->email;
        if(strpos($email, '.') === false)
        {
            return response()->json([
                'email' => 'Enter a valid email.'
            ], 422);
        }
        $user = Users::saveUser($request, $request->oldEmail);
        return response()->json([
            'message' => 'User successfully edited!'
        ], 201);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
