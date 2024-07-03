<?php

declare(strict_types=1);

use App\Enumerate\Account;
use App\Enumerate\State;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->text('password');
            $table->enum('type', Account::values());

            $table->text('postal_code');
            $table->enum('state', State::values());
            $table->string('city');
            $table->string('neighborhood')->nullable();
            $table->string('street')->nullable();
            $table->string('address_complement')->nullable();

            $table->longText('image')->nullable();
            $table->string('slug')->nullable();
            $table->text('biography')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
