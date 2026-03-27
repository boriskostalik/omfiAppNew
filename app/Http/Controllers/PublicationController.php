<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use App\Models\Author;
use App\Models\Issue;
use Illuminate\Http\Request;
use App\Http\Requests\PublicationRequest;

class PublicationController extends Controller
{
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
        $sortField = $request->input('sortField', 'id'); 
        $sortOrder = $request->input('sortOrder', 'desc');  
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
        } elseif (in_array($sortField, ['id', 'title', 'type'], true)) {
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