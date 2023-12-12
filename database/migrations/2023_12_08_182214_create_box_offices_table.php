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
        Schema::create('box_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('pro_businesses');
            $table->string('name');
            $table->string('service');
            $table->foreignId('bank_data_id')->constrained('banks_data');
            $table->integer('isActive')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_offices');
    }
};
