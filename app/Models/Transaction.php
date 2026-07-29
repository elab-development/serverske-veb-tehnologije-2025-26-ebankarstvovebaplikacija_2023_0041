<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['name','amount','date_time','sender_account_id','receiver_account_id','category_id'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function senderAccount():BelongsTo{
        return $this->belongsTo(Account::class);
    }

    public function receiverAccount():BelongsTo{
        return $this->belongsTo(Account::class);
    }

    public function category():BelongsTo{
        return $this->belongsTo(Category::class);
    }
}
