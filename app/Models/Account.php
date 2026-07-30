<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;
    protected $fillable = ['account_number','balance','user_id','currency_id'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function currency():BelongsTo{
        return $this->belongsTo(Currency::class);
    }

    public function sendTransactions():HasMany{
        return $this->hasMany(Transaction::class,'sender_account_id');
    }

    public function receiveTransactions():HasMany{
        return $this->hasMany(Transaction::class,'receiver_account_id');
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
