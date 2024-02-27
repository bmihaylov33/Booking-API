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
        if(!Schema::hasTable('bookings'))
        {
            Schema::create('bookings', function (Blueprint $table) {
               $table->id();
               $table->foreignId('room_id')->constrained();
               $table->foreignId('customer_id')->constrained();
               $table->dateTime('check_in_date');
               $table->dateTime('check_out_date');
               $table->decimal('total_price', 8, 2);
               $table->timestamps();
           });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
