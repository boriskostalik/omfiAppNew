<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Author;
use App\Models\Institute;
use App\Models\Issue;
use App\Models\Publication;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $typ = $request->input('typ', 'publikacie');

        if ($typ === 'autori') {
            return $this->searchAuthors($request);
        }

        return $this->searchPublications($request);
    }

    private function searchPublications(Request $request)
    {
        $perPage     = (int) $request->input('per_page', 10);
        $search      = $request->input('search');
        $year        = $request->input('year');
        $number      = $request->input('number');
        $instituteId = $request->input('institute_id');
        $authorId    = $request->input('author_id');
        $sortKey     = $request->input('sortKey', 'title_asc');

        $query = Publication::query()->with(['authors.institute', 'issue']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('title COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                  ->orWhereHas('keywords', fn($qk) => $qk->whereRaw('name COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"]))
                  ->orWhereHas('authors', function ($qa) use ($search) {
                      $qa->whereRaw('firstname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('surname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if ($year !== null && $year !== '') {
            $query->whereHas('issue', fn ($qi) => $qi->where('year', (int) $year));
        }

        if ($number !== null && $number !== '') {
            $query->whereHas('issue', fn ($qi) => $qi->where('number', (int) $number));
        }

        if ($instituteId) {
            $query->whereHas('authors', function ($qa) use ($instituteId) {
                $qa->where('institute_id', $instituteId);
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
            case 'added_asc':
                $query->orderBy('publications.id', 'asc');
                break;
            case 'added_desc':
                $query->orderBy('publications.id', 'desc');
                break;
            case 'title_asc':
            default:
                $sortKey = 'title_asc';
                $query->orderBy('title', 'asc');
                break;
        }

        $publications = $query->paginate($perPage)->appends($request->query());

        $years = Issue::query()->select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $numbersQuery = Issue::query()->select('number')->distinct();
        if ($year !== null && $year !== '') {
            $numbersQuery->where('year', (int) $year);
        }
        $numbers = $numbersQuery->orderByRaw('(number = 0) ASC')->orderBy('number', 'asc')->pluck('number');

        $institutes = Institute::orderBy('name')->get(['id', 'name']);

        $authors = Author::query()
            ->select('id', 'firstname', 'surname')
            ->orderBy('surname')
            ->orderBy('firstname')
            ->get()
            ->map(fn ($a) => [
                'id'    => $a->id,
                'label' => trim($a->surname . ' ' . $a->firstname),
            ])
            ->values();

        return Inertia::render('SearchPage', [
            'typ'          => 'publikacie',
            'publications' => $publications,
            'per_page'     => $perPage,
            'search'       => $search,
            'filters'      => [
                'year'         => $year,
                'number'       => $number,
                'institute_id' => $instituteId,
                'author_id'    => $authorId,
                'sortKey'      => $sortKey,
            ],
            'options' => [
                'years'      => $years,
                'numbers'    => $numbers,
                'institutes' => $institutes,
                'authors'    => $authors,
            ],
        ]);
    }

    private function searchAuthors(Request $request)
    {
        $perPage     = (int) $request->input('per_page', 10);
        $search      = $request->input('search');
        $instituteId = $request->input('institute_id');
        $sortKey     = $request->input('sortKey', 'name_asc');

        $query = Author::query()->with('institute');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('firstname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('surname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('cleanname COLLATE utf8mb4_unicode_ci LIKE ?', ["%{$search}%"]);
            });
        }

        if ($instituteId) {
            $query->where('institute_id', $instituteId);
        }

        $dir = $sortKey === 'name_desc' ? 'desc' : 'asc';
        $query->orderBy('surname', $dir)->orderBy('firstname', $dir);

        $authors    = $query->paginate($perPage)->appends($request->query());
        $institutes = Institute::orderBy('name')->get(['id', 'name']);

        return Inertia::render('SearchPage', [
            'typ'      => 'autori',
            'authors'  => $authors,
            'per_page' => $perPage,
            'search'   => $search,
            'filters'  => ['institute_id' => $instituteId, 'sortKey' => $sortKey],
            'options'  => ['institutes' => $institutes],
        ]);
    }
}
