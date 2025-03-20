<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publication extends Model
{   
    use HasFactory;
    protected $fillable = [
        'type', 'title', 'title_eng', 'mesc', 'bibtex_id', 'year',
        'actualyear', 'journal', 'volume', 'number', 'month',
        'firstpage', 'lastpage', 'issn', 'isbn', 'url', 'doi',
        'crossref', 'namekey', 'keywords', 'abstract', 'entered_by',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'publication_authors', 'pub_id', 'author_id')
            ->withPivot('rank', 'is_editor'); // Toto zabezpečí, že Laravel načíta `is_editor`
    }
}
