<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('system')) {
            Schema::create('system', function (Blueprint $table) {
                $table->integer('RowID', true);
                $table->string('Code', 255)->nullable();
                $table->string('logo_type', 20)->default('text');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('system');
    }
};
