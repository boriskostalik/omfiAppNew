<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
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

        $publicationsByYear = $author->publications->groupBy('year', true);
        $publicationsByYearFormatted = $publicationsByYear->sortKeysDesc()->map(function ($publications, $year) {
            return [
                'year' => $year,
                'publications' => $publications,
            ];
        })->values();
        return Inertia::render('AuthorDetailPage', [
            'author' => $author,
            'publicationsByYear' => $publicationsByYearFormatted,
        ]);
    }
    public function indexDashboard(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortField = $request->input('sortField', 'firstname');
        $sortOrder = $request->input('sortOrder', 'asc');

        $filters = $request->only(['firstname', 'surname']);

        $query = Author::query()->with('publications');

  
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%");
            });
        }

        
        foreach ($filters as $field => $value) {
            if ($value) {
                $query->where($field, 'like', "%{$value}%");
            }
        }


        if (in_array($sortField, ['firstname', 'surname'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        $authors = $query->paginate($perPage);

        return Inertia::render('Dashboard/Authors', [
            'authors' => $authors,
            'per_page' => $perPage,
            'search' => $search,
            'filters' => $filters,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(AuthorRequest $request)
    {   
        $data = $request->validated();

 
        $author = Author::create($data);

        return redirect()->route('authors.dashboard');
    }

    public function update(AuthorRequest $request, Author $author)
    {   
        $data = $request->validated();
    
        $author->update($data);
    
        return redirect()->route('authors.dashboard');
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return redirect()->route('authors.dashboard');
    }

}