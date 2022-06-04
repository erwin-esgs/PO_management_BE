<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectController extends Controller
{
    public function show(Request $request){
        try {
			$data = Project::select('master_project.*' , 'users.name as created_by')->leftJoin('users', 'users.id', '=', 'master_project.created_by')->get();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    public function detail(Request $request , $id){
        try {
            $data = Project::find($id);
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function add(Request $request){
        try {
            $validatedData = $request->validate([
				'client_name' => ['required'],
				'project_code' => ['required'],
			]);
            if($validatedData){
                $token = JWTAuth::parseToken()->authenticate();
                $now = date("Y-m-d H:i:s");
                $data = new Project();
                $data->client_name = $request->input("client_name");
                $data->project_code = $request->input("project_code");
                if( $request->input("start_date") ){
					$date = $request->input("start_date");
					if (str_contains( $request->input("start_date") , 'T')) { 
						$date = explode("T", $request->input("start_date") )[0];
					}
					$data->start_date = $date;
				}
				if( $request->input("end_date") ){
					$date = $request->input("end_date");
					if (str_contains( $request->input("end_date") , 'T')) { 
						$date = explode("T", $request->input("end_date") )[0];
					}
					$data->end_date = $date;
				}
                if( $request->input("contract_value") )$data->contract_value = $request->input("contract_value");
                if( $request->input("finance") )$data->finance = $request->input("finance");
                if( $request->input("project_team") )$data->project_team = $request->input("project_team");
                //if( $request->input("list_item") )$data->end_date = $request->input("list_item");
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
            $data = Project::find($id);
            if($data){
                if( $request->input("client_name") )$data->client_name = $request->input("client_name");
                if( $request->input("project_code") )$data->project_code = $request->input("project_code");
                if( $request->input("start_date") ){
					$date = $request->input("start_date");
					if (str_contains( $request->input("start_date") , 'T')) { 
						$date = explode("T", $request->input("start_date") )[0];
					}
					$data->start_date = $date;
				}
				if( $request->input("end_date") ){
					$date = $request->input("end_date");
					if (str_contains( $request->input("end_date") , 'T')) { 
						$date = explode("T", $request->input("end_date") )[0];
					}
					$data->end_date = $date;
				}
                if( $request->input("contract_value") )$data->contract_value = $request->input("contract_value");
                if( $request->input("finance") )$data->finance = $request->input("finance");
                if( $request->input("project_team") )$data->project_team = $request->input("project_team");
                //if( $request->input("list_item") )$data->end_date = $request->input("list_item");
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

    public function remove(Request $request ){
        try {
            $data = Project::whereIn('id', $request->post("id"))->delete();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
