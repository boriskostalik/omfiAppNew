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
        'issue_id',
        'type', 'title', 'title_eng',
        'actualyear',
        'firstpage', 'lastpage',
        'doi', 'isbn',
        'bibtex_id',
        'abstract',
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

    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'publication_keywords');
    }
}