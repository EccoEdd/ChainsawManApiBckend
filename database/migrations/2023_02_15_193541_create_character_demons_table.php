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
        Schema::create('character_demons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')
                ->references('id')
                ->on('characters')
                ->onDelete('CASCADE');
            $table->foreignId('demon_id')
                ->references('id')
                ->on('demons')
                ->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_demons');
    }
};
