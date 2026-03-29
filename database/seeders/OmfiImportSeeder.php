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
            foreach ($this->splitSql($sql) as $stmt) {
                DB::statement($stmt);
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function splitSql(string $sql): array
    {
        $statements = [];
        $current    = '';
        $inString   = false;
        $n          = strlen($sql);

        for ($i = 0; $i < $n; $i++) {
            $ch = $sql[$i];
            if ($inString) {
                if ($ch === '\\') {
                    $current .= $ch . ($sql[++$i] ?? '');
                } elseif ($ch === "'") {
                    $inString = false;
                    $current .= $ch;
                } else {
                    $current .= $ch;
                }
            } else {
                if ($ch === "'") {
                    $inString = true;
                    $current .= $ch;
                } elseif ($ch === ';') {
                    $stmt = trim($current);
                    if ($stmt !== '') {
                        $statements[] = $stmt;
                    }
                    $current = '';
                } else {
                    $current .= $ch;
                }
            }
        }
        $stmt = trim($current);
        if ($stmt !== '') {
            $statements[] = $stmt;
        }
        return $statements;
    }
}