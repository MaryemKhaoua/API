<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use Illuminate\Http\Request;


class WalletController extends Controller
{

    public function stock(Request $request) {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'motif' => 'required|string',
        ]);

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->wallet) {
                $montant = $request->input('montant');
                $motif = $request->input('motif');

                $wallet = $user->wallet;
                $wallet->balance += $montant;
                $wallet->save();

                return response()->json([
                    'message' => 'montant added successfully',
                    'newBalance' => $wallet->balance,
                ]);
            } else {
                return response()->json(['error' => 'user does not have a wallet'], 404);
            }
        } else {
            return response()->json(['error' => 'user not authenticated'], 401);
        }
    }

    public function sendMoney(Request $request){

       $data = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $recipient = User::findOrFail($data['sender_id']);
        $montant = $data['montant'];

        $senderWallet = $user->wallet;
        $recipientWallet = $recipient->wallet;

        if ($senderWallet && $recipientWallet && $senderWallet->balance >= $montant) {
            $senderWallet->balance -= $montant;
            $senderWallet->save();

            $recipientWallet->balance += $montant;
            $recipientWallet->save();

            return response()->json(['message' => 'moneyyyy sent successfully']);
        } else {
            return response()->json(['error' => 'invalid transaction or insufficient balance'], 400);
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
