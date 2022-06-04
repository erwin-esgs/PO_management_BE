<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PtController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [LoginController::class, 'login']);
Route::get('/init', [LoginController::class, 'init']);
Route::group(['middleware' => ['auth:api'] ], function() { //['auth:api'] ['auth:sanctum']

	Route::post('/refresh', [LoginController::class, 'refresh']);
	
	Route::get('/user', [UserController::class, 'show']);
	Route::post('/user', [UserController::class, 'add']);
	Route::get('/user/{id}', [UserController::class, 'detail']);
	Route::patch('/user/{id}', [UserController::class, 'edit']);
	Route::delete('/user/{id}', [UserController::class, 'remove']);

    Route::get('/pt', [PtController::class, 'show']);
	Route::post('/pt', [PtController::class, 'add']);
	Route::get('/pt/{id}', [PtController::class, 'detail']);
	Route::patch('/pt/{id}', [PtController::class, 'edit']);
	Route::post('/pt/delete', [PtController::class, 'remove']);

	Route::get('/vendor', [VendorController::class, 'show']);
	Route::post('/vendor', [VendorController::class, 'add']);
	Route::get('/vendor/{id}', [VendorController::class, 'detail']);
	Route::patch('/vendor/{id}', [VendorController::class, 'edit']);
	Route::post('/vendor/delete', [VendorController::class, 'remove']);

	Route::get('/project', [ProjectController::class, 'show']);
	Route::post('/project', [ProjectController::class, 'add']);
	Route::get('/project/{id}', [ProjectController::class, 'detail']);
	Route::patch('/project/{id}', [ProjectController::class, 'edit']);
	Route::post('/project/delete', [ProjectController::class, 'remove']);

	Route::get('/po', [PurchaseOrderController::class, 'show']);
	Route::post('/po', [PurchaseOrderController::class, 'add']);
	Route::get('/po/{id}', [PurchaseOrderController::class, 'detail']);
	Route::patch('/po/{id}', [PurchaseOrderController::class, 'edit']);
	Route::post('/po/delete', [PurchaseOrderController::class, 'remove']);
	
	Route::get('/inv', [InvoiceController::class, 'show']);
	Route::post('/inv', [InvoiceController::class, 'add']);
	Route::get('/inv/{id}', [InvoiceController::class, 'detail']);
	Route::patch('/inv/{id}', [InvoiceController::class, 'edit']);
	Route::post('/inv/delete', [InvoiceController::class, 'remove']);

	Route::post('/dashboard', [DashboardController::class, 'getData']);
	Route::post('/dashboardPo', [DashboardController::class, 'getPo']);
	
	Route::get('/jwt', [UserController::class, 'jwt']);
});

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/