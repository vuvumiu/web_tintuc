<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Newsletter table
        if (!Schema::hasTable('newsletter')) {
            Schema::create('newsletter', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('Email', 255)->nullable();
                $table->boolean('is_active')->default(false);
                $table->string('token', 64)->nullable();
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamp('unsubscribed_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->boolean('is_reviewed')->default(false);
                $table->timestamps();
            });
        }

        // Contact table
        if (!Schema::hasTable('contact')) {
            Schema::create('contact', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('Name', 255)->nullable();
                $table->string('subject', 255)->nullable();
                $table->string('Email', 255)->nullable();
                $table->text('Content')->nullable();
                $table->enum('category', ['consult', 'complaint', 'cooperation', 'other'])->default('consult');
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->boolean('is_reviewed')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('contact');
        Schema::dropIfExists('newsletter');
    }
};
