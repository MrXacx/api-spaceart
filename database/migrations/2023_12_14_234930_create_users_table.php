<?php

declare(strict_types=1);

use Enumerate\Account;
use Enumerate\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
            $table->id('id');
            $table->uuid('token')->unique()->default(new Expression('(UUID())'));
            $table->enum('type', Account::values());
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->string('name');
            $table->longText('image')->nullable();
            $table->string('CEP');
            $table->enum('state', State::values());
            $table->string('city');
            $table->string('neighborhood')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->float('rate', 3, 2, true)->default(0.00);
            $table->text('description')->nullable();
            $table->timestamps();
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
