<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_place_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->unique()->constrained('entities');
            $table->boolean('accepts_reservations')->default(false);
            $table->boolean('takeaway_available')->default(false);
            $table->boolean('delivery_available')->default(false);
            $table->boolean('serves_breakfast')->default(false);
            $table->boolean('serves_lunch')->default(false);
            $table->boolean('serves_dinner')->default(false);
            $table->string('price_range')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_place_details');
    }
};
