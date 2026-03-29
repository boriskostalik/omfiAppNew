<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'von',
        'firstname',
        'email',
        'url',
        'institute_id',
    ];

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_authors', 'author_id', 'pub_id');
    }
}
