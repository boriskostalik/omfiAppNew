<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id', 'entered_by',
        'type', 'title', 'title_eng',
        'actualyear', 'month',
        'journal', 'booktitle', 'publisher', 'series',
        'edition', 'chapter',
        'institution', 'organization', 'school',
        'address', 'location', 'howpublished',
        'firstpage', 'lastpage',
        'doi', 'url', 'issn', 'isbn',
        'mesc', 'bibtex_id', 'namekey', 'crossref',
        'keywords', 'note', 'abstract',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'publication_authors', 'pub_id', 'author_id')
            ->withPivot('rank', 'is_editor');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
}