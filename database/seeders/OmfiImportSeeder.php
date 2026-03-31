<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OmfiImportSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $files = [
            base_path('import_db/importauthors_clean.sql'),
            base_path('import_db/importinstitutes.sql'),
            base_path('import_db/importissues.sql'),
            base_path('import_db/importpublications_clean.sql'),
            base_path('import_db/importpublication_authors.sql'),
        ];

        foreach ($files as $file) {
            if (!File::exists($file)) {
                throw new \RuntimeException("SQL file not found: {$file}");
            }

            $sql = File::get($file);
            DB::unprepared($sql);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}