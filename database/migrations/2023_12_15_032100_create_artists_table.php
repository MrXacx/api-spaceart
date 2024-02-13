<?php

use Enumerate\Art;
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
        Schema::create('artists', function (Blueprint $table) {
            $table->foreignId('id')->unique()->references('id')->on('users')->cascadeOnDelete()->cascadeOnDelete();
            $table->enum('art', Art::values());
            $table->float('wage', places: 2, unsigned: true);
            $table->string('cpf')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
