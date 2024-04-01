<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/logout',[AuthController::class,'logout']);

Route::middleware('auth:sanctum')->post('/createwallet',[WalletController::class,'createWallet']);
Route::middleware('auth:sanctum')->post('/stock',[WalletController::class,'stock']);
Route::middleware('auth:sanctum')->post('/send',[TransactionController::class,'sendMoney']);
Route::middleware('auth:sanctum')->get('/usertransaction',[TransactionController::class,'userTransactions']);
Route::middleware('auth:sanctum')->get('/admintransaction',[TransactionController::class,'adminTransactions']);

