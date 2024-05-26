<?php

use App\Models\Art;
use App\Models\Artist;
use App\Models\Enterprise;
use App\Enumerate\AgreementStatus;
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
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Artist::class)->cascadeOnUpdate();
            $table->foreignIdFor(Enterprise::class)->cascadeOnUpdate();

            $table->text('note');

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->foreignIdFor(Art::class)->cascadeOnUpdate();
            $table->float('price', places: 2, unsigned: true);
            $table->enum('status', AgreementStatus::values())->default(AgreementStatus::SEND->value);

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
