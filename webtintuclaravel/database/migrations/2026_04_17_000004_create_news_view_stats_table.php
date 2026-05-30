<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('news_view_stats')) {
            return;
        }

        Schema::create('news_view_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('news_id');
            $table->date('view_date');
            $table->unsignedInteger('total_views')->default(0);
            $table->timestamps();

            $table->unique(['news_id', 'view_date'], 'news_view_stats_unique');
            $table->index('view_date');
            $table->index('news_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_view_stats');
    }
};
