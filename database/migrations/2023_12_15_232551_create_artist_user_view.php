<?php

use Illuminate\Database\Migrations\Migration;
use Staudenmeir\LaravelMigrationViews\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createOrReplaceView(
            'view_artist_users',
            <<<'SQL'
                SELECT
                u.id, u.name, u.image,
                u.email,
                u.CEP, u.state, u.city, u.neighborhood, u.address,
                u.rate, u.description, u.website, 
                a.art, a.wage
                FROM users AS u, artists AS a
                WHERE u.id = a.id
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropViewIfExists('artist_user_view');
    }
};
