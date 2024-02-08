<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\TransactionFee;
use App\Notifications\TransactionCompleteNotification;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    /**
     * Handles a transaction request.
     *
     * @param  TransactionRequest  $request  The transaction request object.
     *
     */
    public function __invoke(TransactionRequest $request)
    {
        $originCard = Card::getByNumber($request->input('origin_card_number'));
        $destinationCard = Card::getByNumber($request->input('destination_card_number'));

        if (!$this->isEnoughCredit($originCard, $request->input('amount'))) {
            return response()->json(['message' => 'Not enough credit for this transaction'], 400);
        }

        $this->processTransaction($request, $destinationCard, $originCard);

        $this->sendSmsNotification($originCard,$destinationCard, $request->input('amount'));
        return response()->json(['message' => 'Transaction was successful']);
    }

    /**
     * Checks if the origin card has enough credit to cover the amount plus the transaction fee.
     *
     * @param  Card  $originCard  The origin card data.
     * @param  float  $amount  The amount to be deducted from the origin card.
     *
     * @return bool  Returns true if the origin card has enough credit, otherwise false.
     */
    private function isEnoughCredit(Card $originCard, float $amount): bool
    {
        return data_get($originCard->bankAccount()->first(), 'credit') >= ($amount + $this->getTransactionFee());
    }

    /**
     * Retrieves the transaction fee from the configuration.
     *
     * @return float The transaction fee.
     */
    private function getTransactionFee(): float
    {
        return config('bank.transaction_fee');
    }

    /**
     * Processes a transaction by deducting the amount from the origin card, adding it to the destination card,
     * and creating transaction records.
     *
     * @param  TransactionRequest  $request  The request object containing the transaction details.
     * @param  Card  $destinationCard  The destination card object to which the amount will be added.
     * @param  Card  $originCard  The origin card object from which the amount will be deducted.
     *
     * @return void
     */
    private function processTransaction(TransactionRequest $request, Card $destinationCard, Card $originCard): void
    {
        DB::transaction(function () use ($request, $destinationCard, $originCard) {
            $transaction_fee = $this->getTransactionFee();
            $amount = $request->input('amount');

            $originCard->bankAccount()->decrement('credit', $amount + $transaction_fee);
            $destinationCard->bankAccount()->increment('credit', $amount);

            $transaction = $this->createTransactionRecord($destinationCard, $originCard, $amount);
            $this->createTransactionFeeRecord($transaction, $transaction_fee);
        });
    }

    /**
     * Creates a transaction record in the database.
     *
     * @param  Card  $destinationCard  The destination card object.
     * @param  Card  $originCard  The origin card object.
     * @param  float  $amount  The transaction amount.
     *
     * @return Transaction The created transaction record.
     */
    private function createTransactionRecord(Card $destinationCard, Card $originCard, float $amount): Transaction
    {
        return Transaction::query()->create([
            'origin_card_id'      => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount'              => $amount,
            'transaction_date'    => now(),
        ]);
    }

    /**
     * Creates a new transaction fee record.
     *
     * @param  Transaction  $transaction  The transaction data.
     * @param  float  $transaction_fee  The transaction fee amount.
     *
     * @return void
     */
    private function createTransactionFeeRecord(Transaction $transaction, float $transaction_fee): void
    {
        TransactionFee::query()->create([
            'transaction_id' => data_get($transaction, 'id'),
            'fee'            => $transaction_fee,
        ]);
    }

    /**
     * Sends an SMS notification to both the origin and destination card users when a transaction is complete.
     *
     * @param  Card  $originCard  The origin card involved in the transaction.
     * @param  Card  $destinationCard  The destination card involved in the transaction.
     * @param  mixed  $input  Additional input for the notification.
     *
     * @return void
     */
    private function sendSmsNotification(Card $originCard, Card $destinationCard, mixed $input): void
    {
        $originCard->user()->first()->notify(new TransactionCompleteNotification('receiver', $input));
        $destinationCard->user()->first()->notify(new TransactionCompleteNotification('sender', $input));
    }
}
