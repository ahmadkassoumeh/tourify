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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->morphs('bookable');

            $table->date('booking_date');

            $table->enum('status', [
                'pending',
                'confirmed',
                'agency',
                'cancelled',
                'rejected'
            ]);

            $table->foreignId('package_booking_id')
                ->nullable()
                ->constrained('bookings')
                ->nullOnDelete();

            
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
