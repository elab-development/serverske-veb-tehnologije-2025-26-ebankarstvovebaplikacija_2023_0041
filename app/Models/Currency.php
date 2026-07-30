<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = ['name','code'];
    protected $guarded = ['id'];
    public $timestamps = false;

    public function accounts():HasMany {
        return $this->hasMany(Account::class);
    }
}
