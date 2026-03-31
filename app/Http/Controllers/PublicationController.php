<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use App\Models\Author;
use App\Models\Issue;
use App\Models\Keyword;
use Illuminate\Http\Request;
use App\Http\Requests\PublicationRequest;

class PublicationController extends Controller
{
    public function detail($id)
    {
        $publication = Publication::with([
            'issue',
            'keywords',
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

        $query = Publication::query()->with(['authors', 'issue', 'keywords']);
        $authors = Author::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('title COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                  ->orWhereHas('issue', fn ($qi) => $qi->where('year', 'like', "%{$search}%"))
                  ->orWhereHas('authors', function ($qa) use ($search) {
                      $qa->whereRaw('firstname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('surname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if (!empty($filters['title'])) {
            $query->whereRaw('title COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$filters['title']}%"]);
        }

        if (!empty($filters['type'])) {
            $query->whereRaw('type COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$filters['type']}%"]);
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
        $keywordNames = $data['keywords'] ?? [];
        unset($data['keywords']);

        $publication = Publication::create($data);

        $keywordIds = collect($keywordNames)
            ->map(fn($name) => Keyword::firstOrCreate(['name' => trim($name)])->id)
            ->all();
        $publication->keywords()->sync($keywordIds);

        if ($request->has('authors') && is_array($request->authors)) {
            $this->syncAuthors($publication, $request->authors);
        }

        return redirect()->route('publications.dashboard');
    }

    public function update(PublicationRequest $request, Publication $publication)
    {
        $data = $request->validated();
        $keywordNames = $data['keywords'] ?? [];
        unset($data['keywords']);

        $publication->update($data);

        $keywordIds = collect($keywordNames)
            ->map(fn($name) => Keyword::firstOrCreate(['name' => trim($name)])->id)
            ->all();
        $publication->keywords()->sync($keywordIds);

        if ($request->has('authors') && is_array($request->authors)) {
            $this->syncAuthors($publication, $request->authors);
        }

        return redirect()->route('publications.dashboard');
    }

    private function syncAuthors(Publication $publication, array $authors): void
    {
        $publication->authors()->detach();
        foreach ($authors as $author) {
            $authorId = is_array($author) ? $author['id'] : $author;
            $publication->authors()->attach($authorId, [
                'rank' => 1,
                'is_editor' => $author['is_editor'] ?? 'N',
            ]);
        }
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();
        return redirect()->route('publications.dashboard');
    }
}