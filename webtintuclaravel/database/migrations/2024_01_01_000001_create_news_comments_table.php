<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create news table first if not exists
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

        if (!Schema::hasTable('news_comments')) {
            Schema::create('news_comments', function (Blueprint $table) {
                $table->id();
                $table->integer('news_id');
                $table->foreign('news_id')->references('RowID')->on('news')->onDelete('cascade');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->text('content');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('news_comments');
    }
};
