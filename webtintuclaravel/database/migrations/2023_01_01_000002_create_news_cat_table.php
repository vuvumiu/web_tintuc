<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('news_cat')) {
            Schema::create('news_cat', function (Blueprint $table) {
                $table->integer('RowID', true);
                $table->string('Name', 255)->nullable();
                $table->string('Status', 255)->nullable();
                $table->string('image', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('news_cat');
    }
};
