<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->decimal('amount',50);
            $table->dateTime('date_time');
            $table->foreignId('sender_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('receiver_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
