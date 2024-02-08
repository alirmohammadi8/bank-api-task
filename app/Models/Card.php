<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{

    use SoftDeletes, HasFactory;

    protected $fillable = [
        'bank_account_id',
        'card_number',
        'expiry_date',
        'cvv2',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Get the expiry date attribute.
     *
     * @param  string  $value  The value to be parsed as a date.
     *
     * @return string The formatted expiry date in 'y-m' format.
     */
    public function getExpiryDateAttribute($value): string
    {
        return Carbon::parse($value)->format('y-m');
    }


    /**
     * Set the expiry date attribute.
     *
     * @param  string  $value  The value of the expiry date to be set.
     *
     * @return void
     */
    public function setExpiryDateAttribute($value): void
    {
        $this->attributes['expiry_date'] = Carbon::createFromFormat('y-m', $value)->format('Y-m-d');
    }

    /**
     * Get a card by its card number.
     *
     * @param  string  $card_number  The card number of the card to retrieve.
     *
     * @return Card  The card with the specified card number.
     */
    public static function getByNumber($card_number): Card
    {
        return static::where('card_number', $card_number)->first();
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user()
    {
        return $this->bankAccount->belongsTo(User::class);
    }
}
