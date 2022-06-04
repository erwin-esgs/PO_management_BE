<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pt;
use Tymon\JWTAuth\Facades\JWTAuth;

class PtController extends Controller
{
    public function show(Request $request){
        try {
            $data = Pt::select('master_pt.*' , 'users.name as created_by')->leftJoin('users', 'users.id', '=', 'master_pt.created_by')->get();
			$token = auth()->fromUser(auth()->user());
            return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    public function detail(Request $request , $id){
        try {
            $data = Pt::find($id);
            $token = auth()->fromUser(auth()->user());
            return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function add(Request $request){
        try {
            $validatedData = $request->validate([
				'pt_name' => ['required'],
				'full_name' => ['required'],
			]);
            if($validatedData){
                $token = JWTAuth::parseToken()->authenticate();
                $now = date("Y-m-d H:i:s");
                $data = new Pt();
                $data->pt_name = $request->input("pt_name");
                $data->full_name = $request->input("full_name");
				if($request->input("npwp"))$data->npwp = $request->input("npwp");
                if($request->input("siup"))$data->siup = $request->input("siup");
                if($request->input("address"))$data->address = $request->input("address");
                $data->created_by = $token->id;
                $data->created_at = $now;
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
            $data = Pt::find($id);
            if($data){
                if($request->input("pt_name"))$data->pt_name = $request->input("pt_name");
                if($request->input("full_name"))$data->full_name = $request->input("full_name");
                if($request->input("npwp"))$data->npwp = $request->input("npwp");
                if($request->input("siup"))$data->siup = $request->input("siup");
                if($request->input("address"))$data->address = $request->input("address");
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

    public function remove( Request $request ){
        try {
            $data = Pt::whereIn('id', $request->post("id"))->delete();
            $token = auth()->fromUser(auth()->user());
            return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
