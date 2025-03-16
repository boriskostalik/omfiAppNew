<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;




class Author extends Model
{   
    use HasFactory;
    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'publication_authors', 'author_id', 'pub_id');
    }
    
}
