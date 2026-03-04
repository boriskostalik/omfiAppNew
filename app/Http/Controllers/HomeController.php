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
 
}
