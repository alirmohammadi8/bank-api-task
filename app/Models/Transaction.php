<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'origin_card_id',
        'destination_card_id',
        'amount',
        'transaction_date',
    ];

    /**
     * Get the origin card for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship between the model and the origin card.
     */
    public function originCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Card::class, 'origin_card_id');
    }

    /**
     * Retrieve the destination card associated with this entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship instance for the destination card.
     */
    public function destinationCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Card::class, 'destination_card_id');
    }

    /**
     * Retrieve the transaction fee associated with this entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne The relationship instance for the transaction fee.
     */
    public function transactionFee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TransactionFee::class);
    }
}
