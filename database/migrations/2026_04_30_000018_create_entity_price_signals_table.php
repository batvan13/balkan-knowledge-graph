<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_price_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities');
            $table->enum('signal_type', ['observed', 'owner_declared']);
            $table->enum('price_category', ['budget', 'midrange', 'premium', 'luxury'])->nullable();
            $table->string('currency')->nullable();
            $table->decimal('amount_min', 10, 2)->nullable();
            $table->decimal('amount_max', 10, 2)->nullable();
            $table->timestamp('observed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_price_signals');
    }
};
