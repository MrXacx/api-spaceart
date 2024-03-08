<?php

use App\Models\Art;
use App\Models\Enterprise;
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
            $table->foreignIdFor(Enterprise::class)->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('title');
            $table->foreignIdFor(Art::class)->cascadeOnUpdate();
            $table->float('price', places: 2, unsigned: true);
            $table->text('note')->nullable();

            $table->dateTime('start_moment');
            $table->dateTime('end_moment');

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
