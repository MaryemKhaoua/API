<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function sendMoney(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'receiver' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $montant = $request->input('montant');
        $receiverId = $request->input('receiver');

        if (!$user->wallet) {
            return response()->json(['message' => 'Utilisateur sans wallet'], 404);
        }

        if ($user->wallet->balance < $montant) {
            return response()->json(['message' => 'Solde insuffisant'], 403);
        }

        $transaction = new Transaction([
            'user_id' => $user->id,
            'montant' => $montant,
            'receiver' => $receiverId,
        ]);
        $transaction->save();

        $user->wallet->decrement('balance', $montant);

        $receiver = User::findOrFail($receiverId);
        $receiver->wallet->increment('balance', $montant);

        return response()->json(['message' => 'Transaction effectuée avec succès'], 200);
    }

    public function userTransactions()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->orderByDesc('created_at')->get();

        return response()->json(['transactions' => $transactions], 200);
    }

    public function adminTransactions()
    {
        if (Auth::user()->role_id !== 1) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $transactions = Transaction::with(['user', 'receiver'])->orderByDesc('created_at')->get();

        return response()->json(['transactions' => $transactions], 200);
    }
}
