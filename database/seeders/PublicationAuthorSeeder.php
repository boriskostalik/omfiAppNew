<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Publication;
use App\Models\Author;

class PublicationAuthorSeeder extends Seeder
{
    public function run()
    {
        $publication1 = Publication::find(1);
        $publication2 = Publication::find(2);
        $author1 = Author::find(1);
        $author2 = Author::find(2);
        $author3 = Author::find(3);
        $author4 = Author::find(4);

        if ($publication1 && $author1) {
            $publication1->authors()->attach($author1->id, ['rank' => 1, 'is_editor' => 'N']);
            $publication1->authors()->attach($author2->id, ['rank' => 2, 'is_editor' => 'Y']);
        }
        if ($publication2 && $author3) {
            $publication2->authors()->attach($author3->id, ['rank' => 1, 'is_editor' => 'N']);
            $publication2->authors()->attach($author4->id, ['rank' => 1, 'is_editor' => 'N']);
        }
    }
}
