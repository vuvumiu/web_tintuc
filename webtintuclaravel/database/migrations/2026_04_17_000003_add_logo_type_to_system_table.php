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
        if (!Schema::hasTable('system')) {
            return;
        }
        
        if (!Schema::hasColumn('system', 'logo_type')) {
            Schema::table('system', function (Blueprint $table) {
                $table->string('logo_type', 20)->default('text');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system', function (Blueprint $table) {
            $table->dropColumn('logo_type');
        });
    }
};
