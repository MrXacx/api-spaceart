<?php

use App\Models\Artist;
use App\Models\Selective;
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
            $table->foreignIdFor(Artist::class);
            $table->foreignIdFor(Selective::class);
            $table->timestamps();

            $table->primary(['artist_id', 'selective_id']);
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
