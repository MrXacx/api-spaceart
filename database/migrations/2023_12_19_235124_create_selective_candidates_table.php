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
        Schema::create('selective_candidates', function (Blueprint $table) {
            $table->foreignId('artist')->references('id')->on('artists');
            $table->foreignId('selective')->references('id')->on('selectives');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selective_candidates');
    }
};
