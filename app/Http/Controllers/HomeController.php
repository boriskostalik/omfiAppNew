<?php

namespace App\Http\Controllers;
use App\Models\Publication;
use App\Models\Author;
use App\Models\Issue;
use Illuminate\Http\Request;
use Inertia\Inertia;
class HomeController extends Controller
{
    public function index(Request $request)
{
    $latestIssue = Issue::query()
        ->orderByDesc('year')
        ->orderByDesc('number')
        ->first();

    return Inertia::render('HomePage', [
        'latest' => $latestIssue ? [
            'id' => $latestIssue->id,
            'year' => $latestIssue->year,
            'number' => $latestIssue->number,
            'volume' => $latestIssue->volume,
        ] : null,
        'stats' => [
            'publications' => Publication::count(),
            'authors' => Author::count(),
            'issues' => Issue::count(),
        ],
    ]);
}
 
    public function showYear($year)
{   
        $yearInt = (int)$year;
        $issues = Issue::query()
            ->where('year', $yearInt)
            ->orderByRaw('(number = 0) ASC') // 0 (unknown) na koniec
            ->orderBy('number')
            ->get(['id','year','volume','number','pdf_path','published_at']);  
        $issueIds = $issues->pluck('id');
        $publications = Publication::query()
            ->whereIn('issue_id', $issueIds)
            ->with('authors')
            ->with('issue:id,year,volume,number')
            ->orderByRaw('CAST(firstpage AS UNSIGNED) ASC')
            ->orderBy('title')
            ->get();
        return Inertia::render('YearPage', [
            'year' => $yearInt,
            'issues' => $issues,
            'publications' => $publications,
        ]);
}
    
    public function showIssue($year, $number)
{
        $issue = Issue::query()
            ->where('year', (int)$year)
            ->where('number', (int)$number)
            ->firstOrFail();
        $publications = Publication::query()
            ->where('issue_id', $issue->id)
            ->with('authors')
            ->orderByRaw('CAST(firstpage AS UNSIGNED) ASC')
            ->orderBy('title')
            ->get();
        return Inertia::render('IssuePage', [
            'issue' => $issue,
            'publications' => $publications,
        ]);
}
}
