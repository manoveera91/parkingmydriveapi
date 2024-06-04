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
        Schema::create('owner_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auth_owner_id')->constrained('auth_owners')->onDelete('cascade');
            $table->integer('amount_paid');
            $table->dateTime('paid_date');
            $table->string('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_payments');
    }
};
