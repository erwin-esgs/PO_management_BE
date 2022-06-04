<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\Pt;
use App\Models\Vendor;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardController extends Controller
{
    public function getData(Request $request){
		try {
			$query = PurchaseOrder::select(
					'master_pt.id as id_pt',
					'master_pt.pt_name',
					'master_project.id as id_project',
					'master_project.project_code',
					'master_vendor.id as id_vendor',
					'master_vendor.vendor_name',
					'purchase_order.*'
				)
				->leftJoin('master_pt', 'master_pt.id', '=', 'purchase_order.id_pt')
				->leftJoin('master_project', 'master_project.id', '=', 'purchase_order.id_project')
				->leftJoin('master_vendor', 'master_vendor.id', '=', 'purchase_order.id_vendor');
			if($request->input("startDate") && $request->input("endDate")){
				$query->where('purchase_order.tod', '>=' , $request->input("startDate") );
				$query->where('purchase_order.tod', '<=' , $request->input("endDate") );
			}
			if($request->input("id_pt"))$query->where('master_pt.id', (int)$request->input("id_pt") );
			if($request->input("id_project"))$query->where('master_project.id', (int)$request->input("id_project") );
			if($request->input("id_vendor"))$query->where('master_vendor.id', (int)$request->input("id_vendor") );
			$po = $query->get()->all();
			
			//$pt = Pt::select('master_pt.*',\DB::raw(" '[]' payment"))->get()->all();
			$pt = Pt::select('master_pt.*')->get()->all();
			$pt_indexed = array();
			foreach ($pt as $key => $value) {
				//$pt[$key]->payment = [];
				$pt[$key]->total_po = 0;
				$pt[$key]->total_payment = 0;
				$pt[$key]->total_vat = 0;
				//$pt_indexed[$value->id] = $value;
			}
			
			$total_po=0;
			$total_payment=0;
			$total_vat=0;
			// $total_outstanding=0;
			
			foreach ($po as $item) {
			  $total_po = $total_po + $item->value;
			  $total_vat = $total_vat + $item->vat;
			  $payment = json_decode( $item->payment );
			  if($payment){
				foreach ($payment as $value_payment) {
					if(isset($value_payment->payment_date)){
						$total_payment = $total_payment + ( $value_payment->percentage * $item->value / 100 ) ;
					}
				}
			  
				foreach ($pt as $key => $value) {
				  if( $item->id_pt == $value->id ){
					$pt[$key]->total_po = $pt[$key]->total_po + $item->value;
					$pt[$key]->total_vat = $pt[$key]->total_vat + $item->vat;
					foreach ($payment as $value_payment) {
						if(isset($value_payment->payment_date)){
							$pt[$key]->total_payment = $pt[$key]->total_payment + ( $value_payment->percentage * $item->value / 100 ) ;
						}
					}
					break;
				  }
				}
			  }
			}
			
			$data = (object)[
				"total_po" => $total_po, 
				"total_payment" => $total_payment, 
				"total_outstanding" => ($total_po - $total_payment), 
				"total_vat" => $total_vat, 
				"pt" => $pt,
				"req" => $request->input("startdate") ? $request->input("startdate") : null ,
			];
			
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
		} catch (Exception $e) {
            return response()->json($e);
        }
	}
	
	public function getPo(Request $request){
		try {
			$data = [];
			if( $request->input("id_pt") ){
				$query = PurchaseOrder::select(
					'master_pt.id as id_pt',
					'master_pt.pt_name',
					'master_vendor.vendor_name',
					'purchase_order.*',
					'users.name as created_by'
				)
				->leftJoin('users', 'users.id', '=', 'purchase_order.created_by')
				->leftJoin('master_pt', 'master_pt.id', '=', 'purchase_order.id_pt')
				->leftJoin('master_vendor', 'master_vendor.id', '=', 'purchase_order.id_vendor')
				->where('master_pt.id', (int)$request->input("id_pt") );
				if($request->input("startDate") && $request->input("endDate")){
					$query->where('purchase_order.tod', '>=' , $request->input("startDate") );
					$query->where('purchase_order.tod', '<=' , $request->input("endDate") );
				}
				$data = $query->get();
			}
			
			$token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
		} catch (Exception $e) {
            return response()->json($e);
        }
	}
}
