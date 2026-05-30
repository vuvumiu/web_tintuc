<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->unsignedInteger('impression_limit')->default(1)->after('show_close_button')
                ->comment('So lan hien toi da tren moi trinh duyet, 0=khong gioi han');
            $table->unsignedInteger('cooldown_minutes')->default(30)->after('impression_limit')
                ->comment('So phut nghi giua cac lan hien, 0=khong nghi');
            $table->unsignedInteger('show_delay_seconds')->default(2)->after('cooldown_minutes')
                ->comment('So giay cho truoc khi hien popup');
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn([
                'impression_limit',
                'cooldown_minutes',
                'show_delay_seconds',
            ]);
        });
    }
};
