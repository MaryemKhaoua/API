<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class WalletController extends Controller
{
    public function getUuid($id){
        $query = DB::table('wallets')
    ->where('id', $id)
    ->select(DB::raw('CAST(id AS CHAR) AS id'))
    ->first();
        return $query->id;
    }

    public function createWallet(Request $request)
{
    try {
        $request->validate([
            'type' => 'required|string|max:134',
            'balance' => 'required',
        ]);

        $wallet = Wallet::where('type', $request->input('type'))
            ->where('user_id', Auth::id())
            ->first();

        if ($wallet !== null) {
            return response()->json([
                'message' => 'type de compte deja existant'
            ], 401);
        }

        $wallet = Wallet::create([
            'id' => Str::uuid(),
            'type' => $request->input('type'),
            'user_id' => Auth::id(),
            'balance' => $request->input('balance')
        ]);

        return response()->json([
            'message' => 'Wallet créé avec succès',
            'balance' => $wallet->balance,
            'uuid' => $this->getUuid($wallet->id)
        ], 201);
    }
    catch (\Exception $e) {
        return response()->json([
            'error' => 'erreur lors d la creation du wallet: ' . $e->getMessage()
        ], 500);
    }
}
    public function Stock(Request $request) {
        try {
            $request->validate([
                'type' => 'required|string|max:123',
                'balance' => 'required',
            ]);

            $wallet = Wallet::where('type', $request->input('type'))->where('user_id', Auth::id())->first();
            if ($wallet == NULL) {
                return response()->json([
                    'message' => 'wallet n exist pas',
                ], 401);
            }

            $wallet->balance = $wallet->balance + $request->input('balance');
            $wallet->save();

            return response()->json([
                'message' => 'balance ajouté avec succès',
                'new balance' => $wallet->balance,
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWalletRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
