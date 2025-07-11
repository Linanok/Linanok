<?php

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
        Schema::table('link_domain', function (Blueprint $table) {
            $table->index('domain_id');
            $table->index('link_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_domain', function (Blueprint $table) {
            $table->dropIndex('link_domain_domain_id_index');
            $table->dropIndex('link_domain_link_id_index');
        });
    }
};
