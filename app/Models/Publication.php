<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publication extends Model
{   
    use HasFactory;

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'publication_authors', 'pub_id', 'author_id')
            ->withPivot('rank', 'is_editor'); // Toto zabezpečí, že Laravel načíta `is_editor`
    }
}
