<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\Pt;
use App\Models\Vendor;
use Tymon\JWTAuth\Facades\JWTAuth;

class InvoiceController extends Controller
{
    public function show(Request $request){
        try {
			$token = JWTAuth::getToken();
			$decoded = json_decode(base64_decode(explode(".", $token)[1]));
			
            //$data = PurchaseOrder::all();
            $data = Invoice::select(
					'purchase_order.*',
					'master_project.project_code',
					'invoice.*',
					'users.name as created_by'
				)
				->leftJoin('users', 'users.id', '=', 'invoice.created_by')
				->leftJoin('purchase_order', 'purchase_order.id', '=', 'invoice.id_po')
				->leftJoin('master_project', 'master_project.id', '=', 'purchase_order.id_project')
				->where('users.id', '=', $decoded->id)
				->get();
			foreach ($data as $key1 => $value1) {
				$data[$key1]->total = $data[$key1]->value + $data[$key1]->vat;
			}
			
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    public function detail(Request $request , $id){
        try {
            //$data = PurchaseOrder::find($id);
			$data = Invoice::where('invoice.id',$id)
				->select(
					'purchase_order.*',
					'master_pt.*',
					'master_vendor.*',
					'invoice.*',
					'users.name as created_by'
				)
				->leftJoin('users', 'users.id', '=', 'invoice.created_by')
				->leftJoin('purchase_order', 'purchase_order.id', '=', 'invoice.id_po')
				->leftJoin('master_pt', 'master_pt.id', '=', 'purchase_order.id_pt')
				->leftJoin('master_vendor', 'master_vendor.id', '=', 'purchase_order.id_vendor')
				->first();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function add(Request $request){
        try {
            $validatedData = $request->validate([
				'inv_number' => ['required'],
				'id_po' => ['required'],
				'id_project' => ['required'],
				'due_date' => ['required'],
			]);
            if($validatedData){
                $token = JWTAuth::parseToken()->authenticate();
                $now = date("Y-m-d H:i:s");
                $data = new Invoice();
                $data->inv_number = $request->input("inv_number");
                $data->id_po = $request->input("id_po");
                $data->id_project = $request->input("id_project");
                $data->status = 0;
				if( $request->input("due_date") ){
					$date = $request->input("due_date");
					if (str_contains( $request->input("due_date") , 'T')) { 
						$date = explode("T", $request->input("due_date") )[0];
					}
					$data->due_date = $date;
				}
                if( $request->input("value") )$data->value = $request->input("value");
                if( $request->input("vat") )$data->vat = $request->input("vat");
				
                if( $request->input("description") )$data->description = $request->input("description");
                if( $request->input("payment") )$data->payment = $request->input("payment");
                $data->created_by = $token->id;
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
            $data = Invoice::find($id);
            if($data){
                if( $request->input("inv_number") )$data->inv_number = $request->input("inv_number");
                if( $request->input("id_po") )$data->id_po = $request->input("id_po");
                if( $request->input("id_project") )$data->id_project = $request->input("id_project");
                if( $request->input("value") )$data->value = $request->input("value");
                if( $request->input("vat") )$data->vat = $request->input("vat");
                if( $request->input("due_date") ){
					$date = $request->input("due_date");
					if (str_contains( $request->input("due_date") , 'T')) { 
						$date = explode("T", $request->input("due_date") )[0];
					}
					$data->due_date = $date;
				}
                if( $request->input("description") )$data->description = $request->input("description");
                if( $request->input("payment") )$data->payment = $request->input("payment");
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
            $data = Invoice::whereIn('id', $request->post("id"))->delete();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}