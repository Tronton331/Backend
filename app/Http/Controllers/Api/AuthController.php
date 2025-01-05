<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Auth};

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //? input rules for user
        //  Memastikan user memenuhi aturan dalam memasukan input
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required|min:5'
        ]);

        //? If invalid field
        //  Apabila input dari user tidak memenuhi aturan
        if($validator->fails())
        {
            return response()->json([
                "message"=>"Invalid field",
                "errors"=>$validator->errors()
            ], 422);
        }

        //? If user axist in database with correct password
        //  Email dari user ada di database
        //  password dari email benar
        if(Auth::attempt(["email"=>$request->email, "password"=>$request->password]))
        {
            $user = Auth::user();
            $token = $user->createToken("accessToken")->plainTextToken;
            return response()->json([
                "message"=>"Logon success",
                "user"=>[
                    "name"=>$user->name,
                    "email"=>$user->email,
                    "token"=>$token
                ],
            ], 200);
        }
        //? If user or password incorrect
        //  Email dari user tidak ada di database
        //  password dari email salah
        else
        {
            return response()->json(["message"=>"Email or password incorrect"], 401);
        }
    }
}
