<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('room_type'); // Standard, Deluxe, Suite, etc.
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->integer('capacity')->default(2);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->string('image')->nullable();
            $table->json('amenities')->nullable();
            $table->integer('floor')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
