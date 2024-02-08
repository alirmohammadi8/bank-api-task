<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id');
            $table->string('card_number')->unique();
            $table->date('expiry_date');
            $table->integer('cvv2');
            $table->string('status')->default('ACTIVE');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
