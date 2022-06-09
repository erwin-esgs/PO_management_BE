<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\Pt;
use App\Models\Vendor;
use App\Models\Invoice;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardController extends Controller
{
    public function getData(Request $request){
		try {
			$data = Pt::select('master_pt.*')->get()->all();
			$data = json_encode($data);
			$data = json_decode($data);
			foreach ($data as $key1 => $value1) {
				$query = PurchaseOrder::select(
					'master_project.id as id_project',
					'master_project.project_code',
					'master_vendor.id as id_vendor',
					'master_vendor.vendor_name',
					'purchase_order.*',
					'users.name as created_by'
				)
				->leftJoin('users', 'users.id', '=', 'purchase_order.created_by')
				->leftJoin('master_project', 'master_project.id', '=', 'purchase_order.id_project')
				->leftJoin('master_vendor', 'master_vendor.id', '=', 'purchase_order.id_vendor')
				->where('purchase_order.id_pt', (int)$value1->id );
				if($request->input("startDate") && $request->input("endDate")){
					$query->where('purchase_order.tod', '>=' , $request->input("startDate") );
					$query->where('purchase_order.tod', '<=' , $request->input("endDate") );
				}else{
					$query->where('purchase_order.tod', '>=' , date('Y-01-01') );
				}
				if($request->input("id_pt"))$query->where('master_pt.id', (int)$request->input("id_pt") );
				if($request->input("id_project"))$query->where('master_project.id', (int)$request->input("id_project") );
				if($request->input("id_vendor"))$query->where('master_vendor.id', (int)$request->input("id_vendor") );
				
				$data[$key1]->po = $query->get()->all();
				
				foreach ($data[$key1]->po as $key2 => $value2) {
					$query = Invoice::select(
						'invoice.*',
						'users.name as created_by'
					)
					->leftJoin('users', 'users.id', '=', 'invoice.created_by')
					->where('invoice.id_po', $value2->id );
					$data[$key1]->po[$key2]->invoice = $query->get()->all();
				}
				
			}
			
			
            $token = auth()->fromUser(auth()->user());
			return response()->json(["data" => $data , "token" => $token]);
		} catch (Exception $e) {
            return response()->json($e);
        }
	}
	
	
}
