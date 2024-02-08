<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionFee extends Model
{

    protected $fillable = [
        'transaction_id',
        'fee',
    ];

    /**
     * Get the transaction associated with this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The transaction relationship.
     */
    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
