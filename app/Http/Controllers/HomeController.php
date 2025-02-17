<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Získanie dostupných rokov
        $years = Publication::whereNotNull('year')
            ->where('year', '!=', '0000') 
            ->selectRaw('CAST(year AS UNSIGNED) as year') // Prevedie string na číslo
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Spracovanie vyhľadávacieho dotazu
        $query = $request->query('query');
        $results = [];

        if ($query) {
            // Hľadanie autorov podľa spojenej hodnoty meno + priezvisko
            $authors = Author::select(
                    'id',
                    DB::raw("CONCAT(firstname, ' ', surname) as full_name")
                )
                ->where(DB::raw("CONCAT(firstname, ' ', surname)"), 'LIKE', "%{$query}%")
                ->get();

            // Hľadanie publikácií podľa názvu
            $publications = Publication::where('title', 'LIKE', "%{$query}%")
                ->get(['id', 'title']);

            // Spojenie výsledkov do jedného poľa
            $results = collect($authors)->map(fn ($author) => [
                'id' => $author->id,
                'name' => $author->full_name,
                'type' => 'Autor'
            ])->merge(
                $publications->map(fn ($pub) => [
                    'id' => $pub->id,
                    'name' => $pub->title,
                    'type' => 'Publikácia'
                ])
            );
        }

        return Inertia::render('HomePage', [
            'years' => $years,
            'results' => $results,
        ]);
    }

    public function showIssue($year, $issue)
    {
        $publications = Publication::whereYear('year', $year)
            ->where('issue', $issue)
            ->with('authors')
            ->get();

        return Inertia::render('IssuePage', [
            'year' => $year,
            'issue' => $issue,
            'publications' => $publications
        ]);
    }
}
