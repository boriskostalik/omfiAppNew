<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Requests\PublicationRequest;
use App\Models\Author;
use Illuminate\Support\Facades\Auth;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $year = $request->input('year');
        $number = $request->input('number');
        $institute = $request->input('institute');
        $authorId = $request->input('author_id');
        $sortKey = $request->input('sortKey', 'title_asc');

        $query = Publication::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('authors', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%");
                    });
            });
        }

        if ($year) {
            $query->where('year', $year);
        }

        if ($number) {
            $query->where('number', $number);
        }

        if ($institute) {
            $query->whereHas('authors', function ($q) use ($institute) {
                $q->whereRaw('TRIM(authors.institute) = ?', [trim($institute)]);
            });
        }

        if ($authorId) {
            $query->whereHas('authors', function ($q) use ($authorId) {
                $q->where('authors.id', $authorId);
            });
        }

        switch ($sortKey) {
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'year_asc':
                $query->orderBy('year', 'asc');
                break;
            case 'year_desc':
                $query->orderBy('year', 'desc');
                break;
            case 'title_asc':
            default:
                $sortKey = 'title_asc';
                $query->orderBy('title', 'asc');
                break;
        }

        $publications = $query->with('authors')->paginate($perPage);

        $years = Publication::query()
            ->select('year')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $numbersQuery = Publication::query()
            ->selectRaw("TRIM(number) as number")
            ->whereNotNull('number')
            ->whereRaw("TRIM(number) <> ''");

        if ($year) {
            $numbersQuery->where('year', $year);
        }

        $numbers = $numbersQuery
            ->distinct()
            ->orderByRaw("CAST(TRIM(number) AS UNSIGNED) ASC")
            ->pluck('number');

        $institutes = Author::query()
            ->selectRaw("TRIM(institute) as institute")
            ->whereNotNull('institute')
            ->whereRaw("TRIM(institute) <> ''")
            ->distinct()
            ->orderBy('institute')
            ->pluck('institute');

        $authors = Author::query()
            ->select('id', 'firstname', 'surname')
            ->orderBy('surname')
            ->orderBy('firstname')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'label' => trim($a->surname . ' ' . $a->firstname),
            ])
            ->values();

        return Inertia::render('PublicationsPage', [
            'publications' => $publications,
            'per_page' => $perPage,
            'search' => $search,
            'filters' => [
                'year' => $year,
                'number' => $number,
                'institute' => $institute,
                'author_id' => $authorId,
                'sortKey' => $sortKey,
            ],
            'options' => [
                'years' => $years,
                'numbers' => $numbers,
                'institutes' => $institutes,
                'authors' => $authors,
            ],
        ]);
    }

    public function detail($id)
    {
        $publication = Publication::with(['authors' => function ($query) {
            $query->orderBy('firstname', 'desc');
        }])->findOrFail($id);

        return Inertia::render('PublicationDetailPage', [
            'publication' => $publication,
        ]);
    }

    public function indexDashboard(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortField = $request->input('sortField', 'title');
        $sortOrder = $request->input('sortOrder', 'asc');
        $filters = $request->only(['title', 'type', 'year', 'journal']);
        $query = Publication::query()->with('authors');
        $authors = Author::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhereHas('authors', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%");
                    });
            });
        }

        foreach ($filters as $field => $value) {
            if ($value) {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        if (in_array($sortField, ['title', 'type', 'year', 'journal'], true)) {
            $query->orderBy($sortField, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        $publications = $query->paginate($perPage);

        return Inertia::render('Dashboard/Publications', [
            'publications' => $publications,
            'per_page' => $perPage,
            'search' => $search,
            'filters' => $filters,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'entered_by' => Auth::user()->id,
            'authors' => $authors->get(),
        ]);
    }

    public function store(PublicationRequest $request)
    {
        $data = $request->validated();
        $publication = Publication::create($data);
        if ($request->has('authors') && is_array($request->authors)) {
            $authorsData = [];

            foreach ($request->authors as $author) {
                $authorId = is_array($author) ? $author['id'] : $author;

                $authorsData[$authorId] = [
                    'rank' => 1,
                    'is_editor' => $author['is_editor'] ?? 'N'
                ];
            }

            $publication->authors()->attach($authorsData);
        }

        return redirect()->route('publications.dashboard');
    }

    public function update(PublicationRequest $request, Publication $publication)
    {
        $data = $request->validated();

        $publication->update($data);

        if ($request->has('authors') && is_array($request->authors)) {
            $authorsData = [];

            foreach ($request->authors as $author) {
                $authorId = is_array($author) ? $author['id'] : $author;

                $authorsData[$authorId] = [
                    'rank' => 1,
                    'is_editor' => $author['is_editor'] ?? 'N'
                ];
            }

            $publication->authors()->sync($authorsData);
        }

        return redirect()->route('publications.dashboard');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();

        return redirect()->route('publications.dashboard');
    }
}
