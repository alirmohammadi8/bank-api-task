<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Card;
use App\Models\User;
use App\Services\SmsService\SmsServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->instance(SmsServiceInterface::class, Mockery::mock(SmsServiceInterface::class, function ($mock) {
        }));

        $this->originUser = User::factory()->create();
        $this->destinationUser = User::factory()->create();

    }

    public function tearDown(): void
    {
        Mockery::close();  // Clean up all the Mockery instances

        parent::tearDown();
    }

    public function testInvokeTransactionWithSufficientCredit()
    {
        $originBankAccount = BankAccount::factory()->create(['credit' => 3000,'user_id'=>$this->originUser->id]);
        $destinationBankAccount = BankAccount::factory()->create(['credit' => 80,'user_id'=>$this->destinationUser->id]);
        $originCard = Card::factory()->create(['bank_account_id' => $originBankAccount->id]);
        $destinationCard = Card::factory()->create(['bank_account_id' => $destinationBankAccount->id]);

        $response = $this->postJson('/api/transaction', [
            'origin_card_number' => $originCard->card_number,
            'destination_card_number' => $destinationCard->card_number,
            'amount' => 1500
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Transaction was successful']);

        $this->assertDatabaseHas('transactions', [
            'origin_card_id' => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => 1500,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $originBankAccount->id,
            'credit' => 1000,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $destinationBankAccount->id,
            'credit' => 1580,
        ]);

        $this->assertDatabaseHas('transaction_fees', [
            'fee' => 500,
        ]);
    }

    public function testInvokeTransactionWithInsufficientCredit()
    {
        $originBankAccount = BankAccount::factory()->create(['credit' => 100]);
        $destinationBankAccount = BankAccount::factory()->create(['credit' => 80,'user_id'=>$this->destinationUser->id]);
        $originCard = Card::factory()->create(['bank_account_id' => $originBankAccount->id]);
        $destinationCard = Card::factory()->create(['bank_account_id' => $destinationBankAccount->id]);

        $response = $this->postJson('/api/transaction', [
            'origin_card_number' => $originCard->card_number,
            'destination_card_number' => $destinationCard->card_number,
            'amount' => 1500
        ]);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Not enough credit for this transaction']);

        $this->assertDatabaseMissing('transactions', [
            'origin_card_id' => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => 1500,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $originBankAccount->id,
            'credit' => 100,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $destinationBankAccount->id,
            'credit' => 80,
        ]);

        $this->assertDatabaseMissing('transaction_fees', [
            'fee' => 500,
        ]);
    }

    public function testInvokeTransactionWithSufficientCreditAndSpacesInCardNumber()
    {
        $originBankAccount = BankAccount::factory()->create(['credit' => 3000,'user_id'=>$this->originUser->id]);
        $destinationBankAccount = BankAccount::factory()->create(['credit' => 80,'user_id'=>$this->destinationUser->id]);
        $originCard = Card::factory()->create(['bank_account_id' => $originBankAccount->id]);
        $destinationCard = Card::factory()->create(['bank_account_id' => $destinationBankAccount->id]);

        $response = $this->postJson('/api/transaction', [
            'origin_card_number' => chunk_split($originCard->card_number,4,' '),
            'destination_card_number' => chunk_split($destinationCard->card_number,4,' '),
            'amount' => 1500
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Transaction was successful']);

        $this->assertDatabaseHas('transactions', [
            'origin_card_id' => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => 1500,
        ]);
    }

    public function testInvokeTransactionWithSufficientCreditAndSeperatorInCardNumber()
    {
        $originBankAccount = BankAccount::factory()->create(['credit' => 3000,'user_id'=>$this->originUser->id]);
        $destinationBankAccount = BankAccount::factory()->create(['credit' => 80,'user_id'=>$this->destinationUser->id]);
        $originCard = Card::factory()->create(['bank_account_id' => $originBankAccount->id]);
        $destinationCard = Card::factory()->create(['bank_account_id' => $destinationBankAccount->id]);

        $response = $this->postJson('/api/transaction', [
            'origin_card_number' => chunk_split($originCard->card_number,4,'-'),
            'destination_card_number' => chunk_split($destinationCard->card_number,4,'-'),
            'amount' => 1500
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Transaction was successful']);

        $this->assertDatabaseHas('transactions', [
            'origin_card_id' => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => 1500,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $originBankAccount->id,
            'credit' => 1000,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $destinationBankAccount->id,
            'credit' => 1580,
        ]);

        $this->assertDatabaseHas('transaction_fees', [
            'fee' => 500,
        ]);
    }

    public function testInvokeTransactionWithSufficientCreditWithArabicNumbers()
    {
        $originBankAccount = BankAccount::factory()->create(['credit' => 3000,'user_id'=>$this->originUser->id]);
        $destinationBankAccount = BankAccount::factory()->create(['credit' => 80,'user_id'=>$this->destinationUser->id]);
        $originCard = Card::factory()->create(['bank_account_id' => $originBankAccount->id]);
        $destinationCard = Card::factory()->create(['bank_account_id' => $destinationBankAccount->id]);

        $western_numerals = array('0','1','2','3','4','5','6','7','8','9');
        $arabic_numerals = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');


        $response = $this->postJson('/api/transaction', [
            'origin_card_number' => strtr($originCard->card_number,array_combine($western_numerals, $arabic_numerals)),
            'destination_card_number' => strtr($destinationCard->card_number,array_combine($western_numerals, $arabic_numerals)),
            'amount' => 1500
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Transaction was successful']);

        $this->assertDatabaseHas('transactions', [
            'origin_card_id' => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => 1500,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $originBankAccount->id,
            'credit' => 1000,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $destinationBankAccount->id,
            'credit' => 1580,
        ]);

        $this->assertDatabaseHas('transaction_fees', [
            'fee' => 500,
        ]);


    }

    public function testInvokeTransactionWithSufficientCreditWithPersianNumbers()
    {
        $originBankAccount = BankAccount::factory()->create(['credit' => 3000,'user_id'=>$this->originUser->id]);
        $destinationBankAccount = BankAccount::factory()->create(['credit' => 80,'user_id'=>$this->destinationUser->id]);
        $originCard = Card::factory()->create(['bank_account_id' => $originBankAccount->id]);
        $destinationCard = Card::factory()->create(['bank_account_id' => $destinationBankAccount->id]);

        $western_numerals = array('0','1','2','3','4','5','6','7','8','9');
        $persian_numerals = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');


        $response = $this->postJson('/api/transaction', [
            'origin_card_number' => strtr($originCard->card_number,array_combine($western_numerals, $persian_numerals)),
            'destination_card_number' => strtr($destinationCard->card_number,array_combine($western_numerals, $persian_numerals)),
            'amount' => 1500
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Transaction was successful']);

        $this->assertDatabaseHas('transactions', [
            'origin_card_id' => $originCard->id,
            'destination_card_id' => $destinationCard->id,
            'amount' => 1500,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $originBankAccount->id,
            'credit' => 1000,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id'=> $destinationBankAccount->id,
            'credit' => 1580,
        ]);

        $this->assertDatabaseHas('transaction_fees', [
            'fee' => 500,
        ]);
    }
}
