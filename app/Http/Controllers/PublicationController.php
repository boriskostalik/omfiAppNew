<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use App\Models\Author;
use App\Models\Issue;
use Illuminate\Http\Request;
use App\Http\Requests\PublicationRequest;
use Illuminate\Support\Facades\Auth;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $perPage   = (int) $request->input('per_page', 10);
        $search    = $request->input('search');
        $year      = $request->input('year');    
        $number    = $request->input('number');    
        $institute = $request->input('institute');
        $authorId  = $request->input('author_id');
        $sortKey   = $request->input('sortKey', 'title_asc');

        $query = Publication::query()
            ->with(['authors', 'issue']);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('authors', function ($qa) use ($search) {
                      $qa->where('firstname', 'like', "%{$search}%")
                         ->orWhere('surname', 'like', "%{$search}%");
                  });
            });
        }
        if ($year !== null && $year !== '') {
            $query->whereHas('issue', fn ($qi) => $qi->where('year', (int) $year));
        }

        if ($number !== null && $number !== '') {
            $query->whereHas('issue', fn ($qi) => $qi->where('number', (int) $number));
        }
        if ($institute) {
            $query->whereHas('authors', function ($qa) use ($institute) {
                $qa->whereRaw('TRIM(authors.institute) = ?', [trim($institute)]);
            });
        }
        if ($authorId) {
            $query->whereHas('authors', function ($qa) use ($authorId) {
                $qa->where('authors.id', $authorId);
            });
        }
        if (in_array($sortKey, ['year_asc', 'year_desc'], true)) {
            $query->leftJoin('issues', 'issues.id', '=', 'publications.issue_id')
                  ->select('publications.*');
        }

        switch ($sortKey) {
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            case 'year_asc':
                $query->orderBy('issues.year', 'asc')
                      ->orderByRaw('(issues.number = 0) ASC') 
                      ->orderBy('issues.number', 'asc')
                      ->orderBy('title', 'asc');
                break;

            case 'year_desc':
                $query->orderBy('issues.year', 'desc')
                      ->orderByRaw('(issues.number = 0) ASC') 
                      ->orderBy('issues.number', 'desc')
                      ->orderBy('title', 'asc');
                break;

            case 'title_asc':
            default:
                $sortKey = 'title_asc';
                $query->orderBy('title', 'asc');
                break;
        }

$publications = $query->paginate($perPage)->appends($request->query());
        $years = Issue::query()
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        $numbersQuery = Issue::query()
            ->select('number')
            ->distinct();

        if ($year !== null && $year !== '') {
            $numbersQuery->where('year', (int) $year);
        }

        $numbers = $numbersQuery
            ->orderByRaw('(number = 0) ASC') 
            ->orderBy('number', 'asc')
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
        $publication = Publication::with([
            'issue',
            'authors' => function ($query) {
                $query->orderBy('firstname', 'desc');
            },
        ])->findOrFail($id);

        return Inertia::render('PublicationDetailPage', [
            'publication' => $publication,
        ]);
    }

    public function indexDashboard(Request $request)
    {
        $perPage   = (int) $request->input('per_page', 10);
        $search    = $request->input('search');
        $sortField = $request->input('sortField', 'title'); 
        $sortOrder = $request->input('sortOrder', 'asc');  
        $filters = $request->only(['title', 'type', 'year', 'number']);

        $query = Publication::query()->with(['authors', 'issue']);
        $authors = Author::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('issue', fn ($qi) => $qi->where('year', 'like', "%{$search}%"))
                  ->orWhereHas('authors', function ($qa) use ($search) {
                      $qa->where('firstname', 'like', "%{$search}%")
                         ->orWhere('surname', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['title'])) {
            $query->where('title', 'like', "%{$filters['title']}%");
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', "%{$filters['type']}%");
        }

        if (!empty($filters['year'])) {
            $query->whereHas('issue', fn ($qi) => $qi->where('year', (int)$filters['year']));
        }

        if (isset($filters['number']) && $filters['number'] !== '') {
            $query->whereHas('issue', fn ($qi) => $qi->where('number', (int)$filters['number']));
        }

        $sortOrder = $sortOrder === 'desc' ? 'desc' : 'asc';

        if ($sortField === 'year') {
            $query->leftJoin('issues', 'issues.id', '=', 'publications.issue_id')
                  ->select('publications.*')
                  ->orderBy('issues.year', $sortOrder)
                  ->orderByRaw('(issues.number = 0) ASC')
                  ->orderBy('issues.number', $sortOrder);
        } elseif (in_array($sortField, ['title', 'type'], true)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $sortField = 'title';
            $query->orderBy('title', 'asc');
        }

$publications = $query->paginate($perPage)->appends($request->query());

        $issues = Issue::query()
            ->orderByDesc('year')
            ->orderByRaw('(number = 0) ASC')
            ->orderBy('number', 'asc')
            ->get(['id', 'year', 'volume', 'number']);

        return Inertia::render('Dashboard/Publications', [
            'publications' => $publications,
            'per_page' => $perPage,
            'search' => $search,
            'filters' => $filters,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'entered_by' => Auth::user()->id,
            'authors' => $authors->get(),
            'issues' => $issues,
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
                    'is_editor' => $author['is_editor'] ?? 'N',
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
                    'is_editor' => $author['is_editor'] ?? 'N',
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