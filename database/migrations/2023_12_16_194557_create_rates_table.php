<?php

use App\Models\Agreement;
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
        Schema::create('rates', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'author_id')->cascadeOnUpdate();
            $table->foreignIdFor(User::class, 'rated_id')->cascadeOnUpdate();
            $table->foreignIdFor(Agreement::class)->cascadeOnUpdate();

            $table->float('score', 3, 2, true);
            $table->text('note')->nullable();

            $table->timestamps();

            $table->primary(['author_id', 'agreement_id'], 'agreement_rate_pk');
            $table->unique(['rated_id', 'agreement_id']);
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
