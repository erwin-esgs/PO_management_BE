<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use database\factories\Users\UserFactory;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request){
        $credentials = request(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }else{
			$decoded = json_decode(base64_decode(explode(".", $token)[1]));
			//return response()->json($decoded);
			$user = User::find($decoded->id);
			if($user){
				$user->last_login = $decoded->iat;
				if( ($user->save()) ){
					return response()->json($token);
				};
			}
		}
		//return response()->json(['token' => $token]);
		/*
        $email = $request->input("email");
        try {
            $user = User::where('email' , $email)->first();
            if($user){
                if( Hash::check( $request->input("password") , $user->password ) ){
                    //return response()->json( $user );
                    //return response()->json([ "token" => "Bearer ".$user->createToken('auth_token')->plainTextToken ]);
                    //return response()->json([ "token" => "asd" ]);
                    $token = JWTAuth::attempt( $request->only('email', 'password') ) ;
                    return response()->json(compact('token' , 'user'));
                    //return response()->json($token);
                }
            }
            return response()->json("Unauthenticated" , 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
        */
    }
	
	public function refresh()
    {
		$decoded = JWTAuth::parseToken()->authenticate();
		$token = JWTAuth::getToken();
		$decoded = json_decode(base64_decode(explode(".", $token)[1]));
		return response()->json($decoded);
		$token = auth()->fromUser(auth()->user());
        return response()->json($token);
    }

    public function init(Request $request){
        $user = new User();
        $user->name = "ADMIN";
        $user->email  = "admin";
        $user->password = Hash::make('P455w0rd!!!');
        $user->role = 0;
        $user->last_login = 0;
        
        $user->save();
        return response()->json("true");
    }
}
