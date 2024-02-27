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
        if(!Schema::hasTable('rooms'))
        {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('number');
                $table->string('type');
                $table->decimal('price_per_night', 8, 2);
                $table->string('status');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_room_id_foreign');
        });
        Schema::dropIfExists('rooms');
    }
};
