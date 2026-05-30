<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact_replies')) {
            Schema::create('contact_replies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_id');
                $table->unsignedBigInteger('staff_id')->nullable();
                $table->string('staff_name', 150)->nullable();
                $table->text('reply_intro')->nullable();
                $table->text('reply_content');
                $table->text('reply_outro')->nullable();
                $table->string('recipient_email', 150);
                $table->timestamp('sent_at');
                $table->timestamps();

                $table->index('contact_id');
                $table->index('sent_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contact_replies')) {
            Schema::drop('contact_replies');
        }
    }
};
