<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('system')) {
            return;
        }

        if (!Schema::hasColumn('system', 'Description')) {
            Schema::table('system', function (Blueprint $table) {
                $table->longText('Description')->nullable()->after('Code');
            });
        }

        if (!Schema::hasColumn('system', 'Status')) {
            Schema::table('system', function (Blueprint $table) {
                $table->tinyInteger('Status')->default(1)->after('logo_type');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('system')) {
            return;
        }

        Schema::table('system', function (Blueprint $table) {
            if (Schema::hasColumn('system', 'Status')) {
                $table->dropColumn('Status');
            }

            if (Schema::hasColumn('system', 'Description')) {
                $table->dropColumn('Description');
            }
        });
    }
};
