<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institute extends Model
{
    protected $fillable = ['name'];

    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }
}
