<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('account_number')->unique();
            $table->string('status')->default('ACTIVE');
            $table->decimal('credit');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
