<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'balance',
        'type',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(Wallet::class);
    }

}
