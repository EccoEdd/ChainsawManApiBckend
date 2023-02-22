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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 15)
                ->nullable(false);
            $table->string('l_name', 15)
                ->nullable();
            $table->string('type', 20)
                ->nullable();
            $table->boolean('alive')
                ->default(true);
            $table->unsignedTinyInteger('age')
                ->nullable();
            $table->foreignId('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
