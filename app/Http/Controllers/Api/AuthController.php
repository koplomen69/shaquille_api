<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */




     public function loginUser(Request $request){
        // Membuat Rules Untuk validasi
         $rules = [
            "email"=> "required|email",
            "password"=> "required",
        ];

        // Validasi nilai input
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
                "status"=> false,
                "message"=> "Login Process Failed",
                'data' => $validator->errors()->first(),
            ],422);
        }

        // Validasi Email dan Password
        if(!Auth::attempt($request->only(['email','password']))) {
            return response()->json([
                'status'=> false,
                'message'=> 'Email and Password not Valid'
            ],401);
        }

        // Request User
        $user = User::where('email', $request->email)->first();
        // Membuat Token
        $token = $user ->createToken('user_token')->plainTextToken;

        // Responses
        return response()->json([
            'status'=> true,
            'message' => "Login Succesfully",
            'user' => $user,
            'token'=> $token,
        ],200);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
