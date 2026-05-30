<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->integer('RowID', true);
                $table->string('Title', 255)->nullable();
                $table->text('Content')->nullable();
                $table->string('Image', 255)->nullable();
                $table->string('Author', 255)->nullable();
                $table->date('Date')->nullable();
                $table->integer('View')->nullable()->default(0);
                $table->integer('cat_id')->nullable();
                $table->integer('hot')->nullable()->default(0);
                $table->string('tags', 255)->nullable();
                $table->boolean('publish')->nullable()->default(1);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('news');
    }
};
