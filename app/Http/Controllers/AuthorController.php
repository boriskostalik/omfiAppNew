<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use App\Models\Institute;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function detail($id)
    {
        $author = Author::with([
            'institute',
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
                    'year'         => (int) $year,
                    'publications' => $pubs->values(),
                ];
            })
            ->values();

        return Inertia::render('AuthorDetailPage', [
            'author'             => $author,
            'publicationsByYear' => $publicationsByYearFormatted,
        ]);
    }

    public function indexDashboard(Request $request)
    {
        $perPage   = (int) $request->input('per_page', 10);
        $search    = $request->input('search');
        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 'desc');
        $filters   = $request->only(['firstname', 'surname']);

        $query = Author::query()->with(['publications.issue', 'institute']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('firstname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('surname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"]);
            });
        }

        foreach ($filters as $field => $value) {
            if ($value) {
                $query->whereRaw("{$field} COLLATE utf8mb4_unicode_ci LIKE ?", ["%{$value}%"]);
            }
        }

        if (in_array($sortField, ['id', 'firstname', 'surname'], true)) {
            $query->orderBy($sortField, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        $authors    = $query->paginate($perPage)->appends($request->query());
        $institutes = Institute::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Dashboard/Authors', [
            'authors'    => $authors,
            'institutes' => $institutes,
            'per_page'   => $perPage,
            'search'     => $search,
            'filters'    => $filters,
            'sortField'  => $sortField,
            'sortOrder'  => $sortOrder,
        ]);
    }

    public function store(AuthorRequest $request)
    {
        $data = $request->validated();
        $data = $this->resolveInstitute($data);
        Author::create($data);

        return redirect()->route('authors.dashboard');
    }

    public function update(AuthorRequest $request, Author $author)
    {
        $data = $request->validated();
        $data = $this->resolveInstitute($data);
        $author->update($data);

        return redirect()->route('authors.dashboard');
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('authors.dashboard');
    }

    private function resolveInstitute(array $data): array
    {
        $name = trim($data['institute'] ?? '');
        unset($data['institute']);

        if ($name !== '') {
            $institute           = Institute::firstOrCreate(['name' => $name]);
            $data['institute_id'] = $institute->id;
        } else {
            $data['institute_id'] = null;
        }

        return $data;
    }
}
