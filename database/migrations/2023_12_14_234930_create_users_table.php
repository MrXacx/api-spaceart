<?php

declare(strict_types=1);

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
            $table->uuid('id')->primary()->default(uuid_create());
            $table->uuid('token')->unique()->default(uuid_create());
            $table->integer('index')->autoIncrement()->unique();
            $table->string('email')->unique();
            $table->string('phone', 11);
            $table->string('password');
            $table->string('name');
            $table->longText('image');
            $table->string('CEP', 8);
            $table->enum('state', \Enumerate\States::cases());
            $table->string('city');
            $table->string('neighborhood')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->float('rate', 1, 2, true)->default(0);
            $table->text('')->nullable();
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
