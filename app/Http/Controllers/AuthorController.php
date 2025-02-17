<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Author;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::all();

        return Inertia::render('AuthorsPage', [
            'authors' => $authors
        ]);
    }
}
