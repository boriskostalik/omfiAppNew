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
            base_path('import_db/importauthors_fixed.sql'),
            base_path('import_db/importpublications.sql'),
            base_path('import_db/importpublication_authors.sql'),
        ];
        foreach ($files as $file) {
            if (!File::exists($file)) {
                throw new \RuntimeException("SQL file not found: {$file}");
            }
            $sql = File::get($file);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if ($stmt !== '') {
                    DB::statement($stmt);
                }
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}