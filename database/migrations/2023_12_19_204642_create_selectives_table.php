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
        Schema::create('selectives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner')->references('id')->on('enterprises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->float('price', places: 2, unsigned: true);
            $table->dateTime('start_moment');
            $table->dateTime('end_moment');
            $table->enum('art', Art::values());
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selectives');
    }
};
