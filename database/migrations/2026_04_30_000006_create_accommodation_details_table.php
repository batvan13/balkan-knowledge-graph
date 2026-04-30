<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->unique()->constrained('entities')->cascadeOnDelete();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->time('check_in_from')->nullable();
            $table->time('check_in_to')->nullable();
            $table->time('check_out_from')->nullable();
            $table->time('check_out_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_details');
    }
};
