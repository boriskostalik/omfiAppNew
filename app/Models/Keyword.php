<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    protected $fillable = ['name'];

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_keywords');
    }
}
