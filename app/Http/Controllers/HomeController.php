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
        $years = Publication::whereNotNull('year')
        ->where('year', '!=', '0000')
        ->selectRaw('CAST(year AS UNSIGNED) as year')
        ->groupBy('year') 
        ->orderByDesc('year')
        ->paginate(16); 

       

        return Inertia::render('HomePage', [
            'years' => $years,
            
        ]);
    }

 
    public function showYear($year)
    {
        $issues = Publication::whereRaw('CAST(year AS UNSIGNED) = ?', [$year]) 
            ->selectRaw('DISTINCT CAST(number AS UNSIGNED) as number') 
            ->orderByRaw('CAST(number AS UNSIGNED)') 
            ->get();
    
        // Získanie všetkých publikácií pre daný rok
        $publications = Publication::whereRaw('CAST(year AS UNSIGNED) = ?', [$year])
            ->select('*') // Môžeš tu dať konkrétne stĺpce, ak nepotrebuješ všetky
            ->orderByRaw('CAST(number AS UNSIGNED)') // Triedenie podľa čísla vydania
            ->orderBy('title') // Sekundárne triedenie podľa názvu
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
