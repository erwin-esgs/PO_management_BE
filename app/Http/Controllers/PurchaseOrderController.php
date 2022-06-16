<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
//use App\Models\Project;
//use App\Models\Vendor;
use Tymon\JWTAuth\Facades\JWTAuth;

class PurchaseOrderController extends Controller
{
    public function show(Request $request){
        try {
			$token = JWTAuth::getToken();
			$decoded = json_decode(base64_decode(explode(".", $token)[1]));
			
            //$data = PurchaseOrder::all();
            
			$query = PurchaseOrder::select(
					'master_pt.id as id_pt',
					'master_pt.pt_name',
					'master_project.id as id_project',
					'master_project.project_code',
					'master_vendor.id as id_vendor',
					'master_vendor.vendor_name',
					'purchase_order.*',
					'users.name as created_by'
				)
				->leftJoin('users', 'users.id', '=', 'purchase_order.created_by')
				->leftJoin('master_pt', 'master_pt.id', '=', 'purchase_order.id_pt')
				->leftJoin('master_project', 'master_project.id', '=', 'purchase_order.id_project')
				->leftJoin('master_vendor', 'master_vendor.id', '=', 'purchase_order.id_vendor')
				//->where('users.id', '=', $decoded->id)
				->orderBy('purchase_order.po_number', 'desc');
			//if($decoded->role != 0) $query->where('users.id', '=', $decoded->id);
			$data = $query->get()->all();
			
			foreach ($data as $key1 => $value1) {
				$data[$key1]->total = $data[$key1]->value + $data[$key1]->vat;
				$query = Invoice::select(
					'invoice.*'
				)
				->where('invoice.id_po', $value1->id );
				$data[$key1]->invoice = $query->get()->all();
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
			$data = PurchaseOrder::where('purchase_order.id',$id)
				->select(
					'master_pt.*',
					'master_pt.id as id_pt',
					'master_project.*',
					'master_project.id as id_project',
					'master_vendor.*',
					'master_vendor.id as id_vendor',
					'purchase_order.*',
					'users.name as created_by'
				)
				->leftJoin('users', 'users.id', '=', 'purchase_order.created_by')
				->leftJoin('master_pt', 'master_pt.id', '=', 'purchase_order.id_pt')
				->leftJoin('master_project', 'master_project.id', '=', 'purchase_order.id_project')
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
				'po_number' => ['required'],
				'id_pt' => ['required'],
				'id_project' => ['required'],
				'id_vendor' => ['required'],
			]);
            if($validatedData){
                $token = JWTAuth::parseToken()->authenticate();
                $now = date("Y-m-d H:i:s");
                $data = new PurchaseOrder();
                $data->po_number = $request->input("po_number");
                $data->id_pt = $request->input("id_pt");
                $data->id_project = $request->input("id_project");
                $data->id_vendor = $request->input("id_vendor");
                $data->status = 0;
                if( $request->input("value") )$data->value = $request->input("value");
                if( $request->input("vat") )$data->vat = $request->input("vat");
                if( $request->input("top") )$data->top = $request->input("top");
				if( $request->input("tod") ){
					$date = $request->input("tod");
					if (str_contains( $request->input("tod") , 'T')) { 
						$date = explode("T", $request->input("tod") )[0];
					}
					$data->tod = $date;
				}
                if( $request->input("description") )$data->description = $request->input("description");
                //if( $request->input("payment") )$data->payment = $request->input("payment");
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
            $data = PurchaseOrder::find($id);
            if($data){
                if( $request->input("po_number") )$data->po_number = $request->input("po_number");
                if( $request->input("id_pt") )$data->id_pt = $request->input("id_pt");
                if( $request->input("id_project") )$data->id_project = $request->input("id_project");
                if( $request->input("id_vendor") )$data->id_vendor = $request->input("id_vendor");
                if( $request->input("value") )$data->value = $request->input("value");
                if( $request->input("vat") )$data->vat = $request->input("vat");
				if( $request->input("top") )$data->top = $request->input("top");
                if( $request->input("tod") ){
					$date = $request->input("tod");
					if (str_contains( $request->input("tod") , 'T')) { 
						$date = explode("T", $request->input("tod") )[0];
					}
					$data->tod = $date;
				}
                if( $request->input("description") )$data->description = $request->input("description");
                //if( $request->input("payment") )$data->payment = $request->input("payment");
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
            $data = PurchaseOrder::whereIn('id', $request->post("id"))->delete();
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}