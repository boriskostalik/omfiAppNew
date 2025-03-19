<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Author::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%");
            });
        }

        // Get authors with pagination
        $authors = $query->paginate($perPage);

        return Inertia::render('AuthorsPage', [
            'authors' => $authors,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }
    
    public function detail($id)
    {
        $author = Author::with(['publications' => function ($query) {
            $query->orderBy('year', 'desc');
        }])->findOrFail($id);

        // Ensure grouping by value to avoid issues with numeric keys
        $publicationsByYear = $author->publications->groupBy('year', true);

        // Sort and reformat the structure
        $publicationsByYearFormatted = $publicationsByYear->sortKeysDesc()->map(function ($publications, $year) {
            return [
                'year' => $year,
                'publications' => $publications,
            ];
        })->values(); // Ensure array indexing

        return Inertia::render('AuthorDetailPage', [
            'author' => $author,
            'publicationsByYear' => $publicationsByYearFormatted,
        ]);
    }
}