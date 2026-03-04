<?php
namespace App\Http\Controllers;
use App\Models\Issue;

use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index()
    {
        $issues=Issue::query()->orderByDesc('year')->orderByRaw('(number=0)ASC')->orderBy('number','asc')->get(['id','year','volume','number','pdf_path','published_at']);
        $years=$issues->pluck('year')->unique()->values();
        $issuesByYear=$issues->groupBy('year')->map(fn($g)=>$g->values())->toArray();
        return Inertia::render('ArchivePage',['years'=>$years,'issuesByYear'=>$issuesByYear]);
    }

    public function showIssue(Issue $issue)
{
    $publications = $issue->publications()
        ->with('authors')
        ->orderByRaw('CAST(firstpage AS UNSIGNED) ASC')
        ->orderBy('title')
        ->get();
    return Inertia::render('IssuePage', [
        'issue' => [
            'id'           => $issue->id,
            'year'         => $issue->year,
            'volume'       => $issue->volume,
            'number'       => $issue->number,
            'pdf_path'     => $issue->pdf_path,
        ],
        'publications' => $publications,
    ]);
}
}