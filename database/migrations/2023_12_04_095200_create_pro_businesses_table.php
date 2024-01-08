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
        Schema::create('pro_businesses', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('inn');
            $table->string('name_of_business');
            $table->string('form_of_business');
            $table->integer('oked');
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_businesses');
    }
};
