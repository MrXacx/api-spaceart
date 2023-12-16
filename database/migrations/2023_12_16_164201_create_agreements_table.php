<?php

use Enumerate\AgreementStatus;
use Enumerate\Art;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hired')->references('id')->on('artists')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('hirer')->references('id')->on('enterprises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('description');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('art', Art::values());
            $table->enum('status', AgreementStatus::values())->default(AgreementStatus::SEND->value);
            $table->float('price', places:2, unsigned: true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
