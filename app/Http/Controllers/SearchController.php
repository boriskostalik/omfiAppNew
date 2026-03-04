<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Author;
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
        $perPage   = (int) $request->input('per_page', 10);
        $search    = $request->input('search');
        $year      = $request->input('year');
        $number    = $request->input('number');
        $institute = $request->input('institute');
        $authorId  = $request->input('author_id');
        $sortKey   = $request->input('sortKey', 'title_asc');

        $query = Publication::query()->with(['authors', 'issue']);

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

        $years = Issue::query()->select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $numbersQuery = Issue::query()->select('number')->distinct();
        if ($year !== null && $year !== '') {
            $numbersQuery->where('year', (int) $year);
        }
        $numbers = $numbersQuery->orderByRaw('(number = 0) ASC')->orderBy('number', 'asc')->pluck('number');

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
                'year'      => $year,
                'number'    => $number,
                'institute' => $institute,
                'author_id' => $authorId,
                'sortKey'   => $sortKey,
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
        $perPage   = (int) $request->input('per_page', 10);
        $search    = $request->input('search');
        $institute = $request->input('institute');
        $sortKey   = $request->input('sortKey', 'name_asc');

        $query = Author::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('cleanname', 'like', "%{$search}%");
            });
        }

        if ($institute) {
            $query->whereRaw('TRIM(institute) = ?', [trim($institute)]);
        }

        $dir = $sortKey === 'name_desc' ? 'desc' : 'asc';
        $query->orderBy('surname', $dir)->orderBy('firstname', $dir);

        $authors = $query->paginate($perPage)->appends($request->query());

        $institutes = Author::query()
            ->selectRaw("TRIM(institute) as institute")
            ->whereNotNull('institute')
            ->whereRaw("TRIM(institute) <> ''")
            ->distinct()
            ->orderBy('institute')
            ->pluck('institute');

        return Inertia::render('SearchPage', [
            'typ'      => 'autori',
            'authors'  => $authors,
            'per_page' => $perPage,
            'search'   => $search,
            'filters'  => ['institute' => $institute, 'sortKey' => $sortKey],
            'options'  => ['institutes' => $institutes],
        ]);
    }
}
