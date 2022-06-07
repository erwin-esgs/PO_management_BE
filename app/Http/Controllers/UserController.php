<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function jwt(Request $request){
        try {
            $data = JWTAuth::parseToken()->authenticate();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function show(Request $request){
        try {
            $data = User::all();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    public function detail(Request $request , $id){
        try {
            $data = User::find($id);
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function add(Request $request){
        try {
			$token = JWTAuth::parseToken()->authenticate();
            $validatedData = $request->validate([
				'name' => ['required'],
				'email' => ['required'],
				'password' => ['required'],
				'role' => ['required'],
			]);
            if($validatedData && $token->role == 0){
				
                $data = new User();
                $data->name = $request->input("name");
                $data->email = $request->input("email");
                $data->password = Hash::make( $request->input("password") );
                $data->role = $request->input("role");
                $data->last_login = 0;
                if( $data->save() ){
                    $token = auth()->fromUser(auth()->user());
					return response()->json(["data" => $data , "token" => $token]);
                }
            }
            return response()->json(false);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function edit(Request $request , $id){
        try {
			$token = JWTAuth::parseToken()->authenticate();
            $data = User::find($id);
            if($data && $token->role == 0){
                if($request->input("name"))$data->name = $request->input("name");
                if($request->input("role"))$data->role = $request->input("role");
                if($request->input("email"))$data->email = $request->input("email");
                if($request->input("password"))$data->password = Hash::make( $request->input("password") );
				if($data->save()){
                    $token = auth()->fromUser(auth()->user());
					return response()->json(["data" => $data , "token" => $token]);
                }
            }
            return response()->json(false);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function remove(Request $request , $id){
        try {
            $data = User::where('id',$id)->delete();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        //} catch (\Throwable $e) {
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
