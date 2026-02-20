<?php

namespace App\Http\Controllers;
use App\Models\Publication;
use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;
class HomeController extends Controller
{
    public function index(Request $request)
{
    $latest = Publication::query()
        ->whereNotNull('year')
        ->where('year', '!=', '0000')
        ->whereNotNull('number')
        ->where('number', '!=', '')
        ->select([
            'id',
            'year',
            'number',
            'volume',
        ])
        ->orderByRaw('CAST(year AS UNSIGNED) DESC')
        ->orderByRaw('CAST(number AS UNSIGNED) DESC')
        ->first();
    $publicationsCount = Publication::count();
    $authorsCount = Author::count();
    $issuesCount = Publication::query()
        ->whereNotNull('year')
        ->whereNotNull('number')
        ->where('year', '!=', '0000')
        ->where('number', '!=', '')
        ->selectRaw("COUNT(DISTINCT CONCAT(year,'/',number)) as cnt")
        ->value('cnt');

    return Inertia::render('HomePage', [
        'latest' => $latest,  
        'stats' => [
            'publications' => $publicationsCount,
            'authors' => $authorsCount,
            'issues' => $issuesCount,
        ],
    ]);
}
 
    public function showYear($year)
    {
        $issues = Publication::whereRaw('CAST(year AS UNSIGNED) = ?', [$year]) 
            ->selectRaw('DISTINCT CAST(number AS UNSIGNED) as number') 
            ->orderByRaw('CAST(number AS UNSIGNED)') 
            ->get();
        $publications = Publication::whereRaw('CAST(year AS UNSIGNED) = ?', [$year])
            ->select('*') 
            ->orderByRaw('CAST(number AS UNSIGNED)') 
            ->orderBy('title') 
            ->with('authors')
            ->get();
    
        return Inertia::render('YearPage', [
            'year' => $year,
            'issues' => $issues,
            'publications' => $publications,
        ]);
    }
    
    public function showIssue($year, $number)
    {
        $publications = Publication::where('year', $year)
        ->where('number', $number)
        ->with('authors')
        ->get();
        return Inertia::render('IssuePage', [
            'year' => $year,
            'number' => $number,
            'publications' => $publications
        ]);
    }
    
    
    
    
    
    

}
