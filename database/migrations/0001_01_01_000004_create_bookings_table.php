<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('guests')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
