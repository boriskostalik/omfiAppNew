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
        $perPage = (int) $request->input('per_page', 10);
        $search  = $request->input('search');

        $query = Author::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%");
            });
        }

        $authors = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('AuthorsPage', [
            'authors' => $authors,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }

    public function detail($id)
    {
            $author = Author::with([
            'publications.issue:id,year,volume,number',
            'publications.authors',
        ])->findOrFail($id);
        $publications = $author->publications->sort(function ($a, $b) {
            $ay = $a->issue?->year ?? -1;
            $by = $b->issue?->year ?? -1;
            if ($ay !== $by) return $by <=> $ay;

            $an = $a->issue?->number ?? 0;
            $bn = $b->issue?->number ?? 0;
            if ($an === 0 && $bn !== 0) return 1;
            if ($bn === 0 && $an !== 0) return -1;
            if ($an !== $bn) return $bn <=> $an;

            return strcmp((string)$a->title, (string)$b->title);
        });
        $publicationsByYear = $publications->groupBy(function ($p) {
            return $p->issue?->year ?? 0;
        });

        $publicationsByYearFormatted = $publicationsByYear
            ->sortKeysDesc()
            ->map(function ($pubs, $year) {
                return [
                    'year' => (int) $year,
                    'publications' => $pubs->values(),
                ];
            })
            ->values();

        return Inertia::render('AuthorDetailPage', [
            'author' => $author,
            'publicationsByYear' => $publicationsByYearFormatted,
        ]);
    }

    public function indexDashboard(Request $request)
    {
        $perPage   = (int) $request->input('per_page', 10);
        $search    = $request->input('search');
        $sortField = $request->input('sortField', 'firstname');
        $sortOrder = $request->input('sortOrder', 'asc');
        $filters   = $request->only(['firstname', 'surname']);
        $query = Author::query()->with('publications.issue');

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
        if (in_array($sortField, ['firstname', 'surname'], true)) {
            $query->orderBy($sortField, $sortOrder === 'desc' ? 'desc' : 'asc');
        }
        $authors = $query->paginate($perPage)->appends($request->query());
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
        Author::create($data);

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