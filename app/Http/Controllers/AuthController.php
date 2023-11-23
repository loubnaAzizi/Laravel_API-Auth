<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        else{
            $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);
            $token= $user->createToken($user->email.'_token')->plainTextToken;
            return response()->json([
                "status" => 200,
                "userName"=>$user->name,
                "token"=>$token,
                "message"=>'Registred Successfully',

            ]);
        }       
    }



    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }
        else
        {
            $user=User::where('email',$request->email)->first();
            if(!$user ||! Hash::check($request->password,$user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credentials'
                ]);
            }
            else
            {
                $token= $user->createToken($user->email.'_token')->plainTextToken;
                return response()->json([
                    "status" => 200,
                    "userName"=>$user->name,
                    "token"=>$token,
                    "message"=>'Loged in Successfully',
    
                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
       
        // auth()->user()->tokens()->delete();
        return response()->json([
            "status" => 200,
           
            "message"=>'Loged Out Successfully',

        ]);
    }
}
