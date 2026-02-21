<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('publications', 'issue_id')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->unsignedBigInteger('issue_id')->nullable()->after('id');
                $table->index('issue_id');
            });
        }
        $dbName = DB::getDatabaseName();
        $fkExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $dbName)
            ->where('TABLE_NAME', 'publications')
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->where('CONSTRAINT_NAME', 'publications_issue_id_foreign')
            ->exists();
        if (!$fkExists) {
            DB::statement("
                ALTER TABLE publications
                ADD CONSTRAINT publications_issue_id_foreign
                FOREIGN KEY (issue_id) REFERENCES issues(id)
                ON DELETE SET NULL
            ");
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE publications DROP FOREIGN KEY publications_issue_id_foreign");
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('publications', function (Blueprint $table) {
                $table->dropIndex(['issue_id']);
            });
        } catch (\Throwable $e) {
        }

        if (Schema::hasColumn('publications', 'issue_id')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->dropColumn('issue_id');
            });
        }
    }
};