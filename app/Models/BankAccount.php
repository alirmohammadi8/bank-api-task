<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{

    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'status',
        'credit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
