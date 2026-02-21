<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

         
            $rows = DB::table('publications')
                ->selectRaw("
                    year,
                    TRIM(volume) AS volume_trim,
                    TRIM(number) AS number_trim
                ")
                ->whereNotNull('year')
                ->whereNotNull('number')
                ->groupBy('year', DB::raw("TRIM(volume)"), DB::raw("TRIM(number)"))
                ->get();

            foreach ($rows as $r) {
                $yearInt = (int) $r->year; 
                $volume = ($r->volume_trim === '' ? null : $r->volume_trim);
                $numberInt = ($r->number_trim === '' ? 0 : (int) $r->number_trim);

                DB::table('issues')->updateOrInsert(
                    [
                        'year' => $yearInt,
                        'volume' => $volume,
                        'number' => $numberInt,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            DB::statement("
                UPDATE publications p
                JOIN issues i
                  ON i.year = CAST(p.year AS UNSIGNED)
                 AND IFNULL(i.volume,'') = IFNULL(NULLIF(TRIM(p.volume),''),'')
                 AND i.number = IF(TRIM(p.number) = '', 0, CAST(TRIM(p.number) AS UNSIGNED))
                SET p.issue_id = i.id
            ");
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('publications')->update(['issue_id' => null]);
            DB::table('issues')->delete();
        });
    }
};