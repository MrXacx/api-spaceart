<?php

use App\Models\Art;
use App\Models\User;
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
            $table->foreignIdFor(User::class, 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->primary('id');

            $table->string('cpf')->unique();
            $table->date('birthday');

            $table->foreignIdFor(Art::class)->cascadeOnUpdate();

            $table->float('wage', places: 2, unsigned: true);

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
