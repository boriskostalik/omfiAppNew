<?php
namespace App\Http\Controllers;
use App\Models\Issue;
use App\Models\Publication;
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

    public function showYear($year)
    {
        $yearInt=(int)$year;
        $issues=Issue::query()->where('year',$yearInt)->orderByRaw('(number=0)ASC')->orderBy('number','asc')->get(['id','year','volume','number','pdf_path','published_at']);
        $issueIds=$issues->pluck('id');
        $publications=Publication::query()->whereIn('issue_id',$issueIds)->with(['authors','issue:id,year,volume,number'])->orderByRaw('CAST(firstpage AS UNSIGNED)ASC')->orderBy('title')->get();
        return Inertia::render('YearPage',['year'=>$yearInt,'issues'=>$issues,'publications'=>$publications]);
    }

    public function showIssue(Issue $issue)
    {
        $publications=$issue->publications()->with('authors')->orderByRaw('CAST(firstpage AS UNSIGNED)ASC')->orderBy('title')->get();
        return Inertia::render('IssuePage',['issue'=>$issue,'publications'=>$publications]);
    }
}