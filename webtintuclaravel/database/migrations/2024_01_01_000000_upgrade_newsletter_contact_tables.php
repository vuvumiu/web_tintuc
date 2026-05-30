<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Đổi tên cột tương thích MariaDB/MySQL cũ (không dùng RENAME COLUMN).
     */
    private function renameColumnMysql(string $table, string $from, string $to, string $definitionSql): void
    {
        if (! Schema::hasColumn($table, $from) || Schema::hasColumn($table, $to)) {
            return;
        }
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE `{$table}` CHANGE `{$from}` `{$to}` {$definitionSql}");
        } else {
            Schema::table($table, function (Blueprint $blueprint) use ($from, $to) {
                $blueprint->renameColumn($from, $to);
            });
        }
    }

    public function up(): void
    {
        if (Schema::hasTable('newsletter')) {
            Schema::table('newsletter', function (Blueprint $table) {
                if (!Schema::hasColumn('newsletter', 'is_active')) {
                    $table->boolean('is_active')->default(false)->after('Email');
                }
                if (!Schema::hasColumn('newsletter', 'token')) {
                    $table->string('token', 64)->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('newsletter', 'subscribed_at')) {
                    $table->timestamp('subscribed_at')->nullable()->after('token');
                }
                if (!Schema::hasColumn('newsletter', 'unsubscribed_at')) {
                    $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
                }
                if (!Schema::hasColumn('newsletter', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('unsubscribed_at');
                }
            });
            $this->renameColumnMysql('newsletter', 'IsViews', 'is_reviewed', 'TINYINT(1) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('contact')) {
            Schema::table('contact', function (Blueprint $table) {
                if (!Schema::hasColumn('contact', 'subject')) {
                    $table->string('subject', 255)->nullable()->after('Name');
                }
                if (!Schema::hasColumn('contact', 'category')) {
                    $table->enum('category', ['consult', 'complaint', 'cooperation', 'other'])
                          ->default('consult')
                          ->after('subject');
                }
                if (!Schema::hasColumn('contact', 'priority')) {
                    $table->enum('priority', ['low', 'medium', 'high'])
                          ->default('medium')
                          ->after('category');
                }
                if (!Schema::hasColumn('contact', 'assigned_to')) {
                    $table->unsignedBigInteger('assigned_to')->nullable()->after('priority');
                }
                if (!Schema::hasColumn('contact', 'admin_note')) {
                    $table->text('admin_note')->nullable()->after('assigned_to');
                }
                if (!Schema::hasColumn('contact', 'replied_at')) {
                    $table->timestamp('replied_at')->nullable()->after('admin_note');
                }
                if (!Schema::hasColumn('contact', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('replied_at');
                }
            });
            $this->renameColumnMysql('contact', 'IsViews', 'is_reviewed', 'TINYINT(1) NOT NULL DEFAULT 0');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('newsletter')) {
            Schema::table('newsletter', function (Blueprint $table) {
                $columns = ['is_active', 'token', 'subscribed_at', 'unsubscribed_at', 'ip_address'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('newsletter', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
            $this->renameColumnMysql('newsletter', 'is_reviewed', 'IsViews', 'TINYINT(1) NOT NULL DEFAULT 0');
        }

        if (Schema::hasTable('contact')) {
            Schema::table('contact', function (Blueprint $table) {
                $columns = ['subject', 'category', 'priority', 'assigned_to', 'admin_note', 'replied_at', 'ip_address'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('contact', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
            $this->renameColumnMysql('contact', 'is_reviewed', 'IsViews', 'TINYINT(1) NOT NULL DEFAULT 0');
        }
    }
};
