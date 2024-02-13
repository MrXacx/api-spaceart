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
            'view_enterprise_users',
            <<<'SQL'
                SELECT
                u.id, u.name, e.company_name, u.image,
                u.email, u.phone, e.cnpj,
                u.postal_code, u.state, u.city, u.neighborhood, u.address,
                u.rate, u.description, u.website
                
                FROM users AS u, enterprises AS e
                WHERE u.id = e.id
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropViewIfExists('enterprise_user_view');
    }
};
