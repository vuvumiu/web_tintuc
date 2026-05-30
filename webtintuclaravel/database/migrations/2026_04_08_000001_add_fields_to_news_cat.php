<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_cat')) {
            return;
        }
        
        if (!Schema::hasColumn('news_cat', 'image')) {
            Schema::table('news_cat', function (Blueprint $table) {
                $table->string('image', 255)->nullable();
            });
        }
        if (!Schema::hasColumn('news_cat', 'color')) {
            Schema::table('news_cat', function (Blueprint $table) {
                $table->string('color', 20)->default('#6c757d')->nullable();
            });
        }
        if (!Schema::hasColumn('news_cat', 'description')) {
            Schema::table('news_cat', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('news_cat', function (Blueprint $table) {
            if (Schema::hasColumn('news_cat', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('news_cat', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('news_cat', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
