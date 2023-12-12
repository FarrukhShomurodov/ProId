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
        Schema::create('banks_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('pro_businesses');
            $table->string('name');
            $table->string('name_of_banking_akkaunt');
            $table->integer('mfo');
            $table->integer('payment_account');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks_data');
    }
};
