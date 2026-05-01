<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_entity_id')->constrained('entities');
            $table->foreignId('to_entity_id')->constrained('entities');
            $table->enum('relation_type', ['located_in', 'near', 'part_of']);
            $table->timestamps();

            $table->unique(['from_entity_id', 'to_entity_id', 'relation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_relations');
    }
};
