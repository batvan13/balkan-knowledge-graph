<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attraction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->unique()->constrained('entities');
            $table->boolean('is_natural')->nullable();
            $table->boolean('is_cultural')->nullable();
            $table->boolean('is_indoor')->nullable();
            $table->boolean('is_outdoor')->nullable();
            $table->boolean('is_free')->nullable();
            $table->boolean('has_entry_fee')->nullable();
            $table->unsignedSmallInteger('estimated_visit_minutes')->nullable();
            $table->boolean('is_family_friendly')->nullable();
            $table->boolean('is_accessible')->nullable();
            $table->boolean('is_seasonal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attraction_details');
    }
};
