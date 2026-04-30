<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_type_id');
            $table->unsignedBigInteger('place_id');
            $table->foreignId('user_id')->constrained('users');
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
