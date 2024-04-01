<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function sendMoney(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'type' => 'required|string',
        'montant' => 'required',
    ]);

    try {
        $receiver = User::where('name', $request->input('name'))->first();

        if ($receiver === NULL) {
            return response()->json([
                'message' => 'User Not Found!'
            ], 401);
        }

        $wallet = Wallet::where('user_id', $receiver->id)
            ->where('type', $request->input('type'))
            ->first();

        if ($wallet === NULL) {
            return response()->json([
                'message' => 'Reciever does not have the same type of wallet!'
            ], 401);
        }


        $sender_id = Auth::id();

        $balance = Wallet::where('user_id', $sender_id)
            ->where('type', $request->input('type'))
            ->value('balance');

        if ($balance < $request->input('montant')) {
            return response()->json([
                'message' => 'Insufficient Balance'
            ], 401);
        }

        Transaction::create([
            'sender' => $sender_id,
            'montant' => $request->input('montant'),
            'receiver' => $receiver->id
        ]);

        $senderBalance = $balance - $request->input('montant');

        Wallet::where('user_id', $sender_id)
            ->where('type', $request->input('type'))
            ->update(['balance' => $senderBalance]);

        $receiverBalance = $balance + $request->input('montant');

        Wallet::where('user_id', $receiver->id)
            ->where('type', $request->input('type'))
            ->update(['balance' => $receiverBalance]);

        return response()->json([
            'message' => 'montant sent successfully',
        ], 201);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}
    public function userTransactions(Request $request)
    {
        $type = $request->input('type');
        $user = Auth::user();
        $transactions = Transaction::where('type', $type)
            ->where('sender', $user->id)
            ->orWhere('receiver', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['transactions' => $transactions], 200);
    }

    public function adminTransactions(Request $request)
    {
        $type = $request->input('type');
        if (Auth::user()->role_id !== 1) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé'], 403);
        }

        $transactions = Transaction::where('type', $type)
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['transactions' => $transactions], 200);
    }
}

