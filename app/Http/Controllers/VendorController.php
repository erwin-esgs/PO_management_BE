<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Tymon\JWTAuth\Facades\JWTAuth;

class VendorController extends Controller
{
    public function show(Request $request){
        try {
			$data = Vendor::select('master_vendor.*' , 'users.name as created_by')->leftJoin('users', 'users.id', '=', 'master_vendor.created_by')->get();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    public function detail(Request $request , $id){
        try {
            $data = Vendor::find($id);
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function add(Request $request){
        try {
            $validatedData = $request->validate([
				'vendor_name' => ['required'],
			]);
            if($validatedData){
                $token = JWTAuth::parseToken()->authenticate();
                $now = date("Y-m-d H:i:s");
                $data = new Vendor();
                $data->vendor_name = $request->input("vendor_name");
                if( $request->input("email") )$data->email = $request->input("email");
                if( $request->input("phone") )$data->phone = $request->input("phone");
                if( $request->input("contact") )$data->contact = $request->input("contact");
                if( $request->input("manager") )$data->manager = $request->input("manager");
                if( $request->input("bank_acc") )$data->bank_acc = $request->input("bank_acc");
                if( $request->input("description") )$data->description = $request->input("description");
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
            $data = Vendor::find($id);
            if($data){
                if( $request->input("vendor_name") )$data->vendor_name = $request->input("vendor_name");
                if( $request->input("email") )$data->email = $request->input("email");
                if( $request->input("phone") )$data->phone = $request->input("phone");
                if( $request->input("contact") )$data->contact = $request->input("contact");
                if( $request->input("manager") )$data->manager = $request->input("manager");
                if( $request->input("bank_acc") )$data->bank_acc = $request->input("bank_acc");
                if( $request->input("description") )$data->description = $request->input("description");
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

    public function remove(Request $request){
        try {
            $data = Vendor::whereIn('id', $request->post("id"))->delete();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
