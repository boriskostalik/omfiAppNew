<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('publications')->insert([
            [
                'id' => 1, 'entered_by' => 1, 'year' => '2006', 'actualyear' => '2006',
                'title' => 'Iracionálne rovnice a nerovnice. Je postup korektný?',
                'title_eng' => 'Irrational Equations and Inequations. Is the Method Correct?',
                'mesc' => '', 'bibtex_id' => 'dobos:06:a', 'pub_type' => '', 'type' => 'Article',
                'survey' => 0, 'mark' => 5, 'series' => '', 'volume' => '35', 'publisher' => '',
                'location' => '', 'issn' => '1335-4981', 'isbn' => '', 'firstpage' => '1',
                'lastpage' => '4', 'journal' => 'Obzory matematiky, fyziky a informatiky',
                'booktitle' => '', 'number' => '3', 'institution' => '', 'address' => '',
                'chapter' => '0', 'edition' => '', 'howpublished' => '', 'month' => '',
                'organization' => '', 'school' => '', 'note' => 'This paper is devoted to radical equations...',
                'keywords' => '', 'abstract' => '', 'url' => '2', 'doi' => '', 'crossref' => '',
                'namekey' => '', 'userfields' => '', 'specialchars' => 'TRUE',
                'cleanjournal' => 'Obzory matematiky, fyziky a informatiky',
                'cleantitle' => 'Iracionalne rovnice a nerovnice. Je postup korektny?',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 2, 'entered_by' => 1, 'year' => '2006', 'actualyear' => '2006',
                'title' => 'Poznámka o pomere hyperobjemov simplexov afinného priestoru An',
                'title_eng' => 'Note about the Ratio Hypervolume of Simplicies in the Affine Space An',
                'mesc' => NULL, 'bibtex_id' => '', 'pub_type' => '', 'type' => 'Article',
                'survey' => 0, 'mark' => 5, 'series' => '', 'volume' => '35', 'publisher' => '',
                'location' => '', 'issn' => '1335-4981', 'isbn' => '', 'firstpage' => '5',
                'lastpage' => '16', 'journal' => 'Obzory matematiky, fyziky a informatiky',
                'booktitle' => '', 'number' => '3', 'institution' => '', 'address' => '',
                'chapter' => '0', 'edition' => '', 'howpublished' => '', 'month' => '',
                'organization' => '', 'school' => '', 'note' => 'In this paper we emphasize some...',
                'keywords' => '', 'abstract' => '', 'url' => '', 'doi' => '', 'crossref' => '',
                'namekey' => '', 'userfields' => '', 'specialchars' => 'FALSE',
                'cleanjournal' => 'Obzory matematiky, fyziky a informatiky',
                'cleantitle' => 'Poznámka o pomere hyperobjemov simplexov afinného priestoru An',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 3, 'entered_by' => 1, 'year' => '2006', 'actualyear' => '2006',
                'title' => 'Iracionálne čísla ako výsledok limitného procesu',
                'title_eng' => 'Irrational Number as a Result of Limit Process',
                'mesc' => NULL, 'bibtex_id' => '', 'pub_type' => '', 'type' => 'Article',
                'survey' => 0, 'mark' => 5, 'series' => '', 'volume' => '35', 'publisher' => '',
                'location' => '', 'issn' => '1335-4981', 'isbn' => '', 'firstpage' => '1',
                'lastpage' => '8', 'journal' => 'Obzory matematiky, fyziky a informatiky',
                'booktitle' => '', 'number' => '1', 'institution' => '', 'address' => '',
                'chapter' => '0', 'edition' => '', 'howpublished' => '', 'month' => '',
                'organization' => '', 'school' => '', 'note' => 'This paper deals with number √2...',
                'keywords' => '', 'abstract' => '', 'url' => '', 'doi' => '', 'crossref' => '',
                'namekey' => '', 'userfields' => '', 'specialchars' => 'FALSE',
                'cleanjournal' => 'Obzory matematiky, fyziky a informatiky',
                'cleantitle' => 'Iracionálne čísla ako výsledok limitného procesu',
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);
    }
}
