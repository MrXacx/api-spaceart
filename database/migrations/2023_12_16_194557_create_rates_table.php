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
        Schema::create('rates', function (Blueprint $table) {
            $table->foreignId('author')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('agreement')->references('id')->on('agreements')->cascadeOnDelete()->cascadeOnUpdate();
            $table->float('score', 3, 2, true);
            $table->text('description');
            $table->timestamps();

            $table->primary(['author', 'agreement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
