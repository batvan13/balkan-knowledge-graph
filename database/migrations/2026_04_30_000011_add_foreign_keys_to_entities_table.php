<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->foreign('entity_type_id')->references('id')->on('entity_types');
            $table->foreign('place_id')->references('id')->on('places');
        });
    }

    public function down(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->dropForeign(['entity_type_id']);
            $table->dropForeign(['place_id']);
        });
    }
};
