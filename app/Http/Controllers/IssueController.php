<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function index()
    {
        $issues = Issue::query()
            ->orderByDesc('year')
            ->orderByRaw('(number = 0) ASC')
            ->orderBy('number', 'asc')
            ->withCount('publications')
            ->paginate(20);

        return Inertia::render('Dashboard/Issues', [
            'issues' => $issues,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year'         => 'required|numeric',
            'volume'       => 'nullable|string|max:16',
            'number'       => 'required|numeric',
            'published_at' => 'nullable|date',
            'pdf'          => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('pdfs', 'public');
        }

        Issue::create([
            'year'         => (int) $data['year'],
            'volume'       => $data['volume'] ?? null,
            'number'       => (int) $data['number'],
            'published_at' => $data['published_at'] ?? null,
            'pdf_path'     => $pdfPath,
        ]);

        return redirect()->route('issues.dashboard')->with('success', 'Issue vytvorené.');
    }

    public function update(Request $request, Issue $issue)
    {
        $data = $request->validate([
            'year'         => 'required|numeric',
            'volume'       => 'nullable|string|max:16',
            'number'       => 'required|numeric',
            'published_at' => 'nullable|date',
            'pdf'          => 'nullable|file|mimes:pdf|max:20480',
            'remove_pdf'   => 'nullable|string',
        ]);

        // Odstránenie PDF
        if ($request->input('remove_pdf') === 'true' && $issue->pdf_path) {
            Storage::disk('public')->delete($issue->pdf_path);
            $issue->pdf_path = null;
        }

        // Nahratie nového PDF
        if ($request->hasFile('pdf')) {
            if ($issue->pdf_path) {
                Storage::disk('public')->delete($issue->pdf_path);
            }
            $issue->pdf_path = $request->file('pdf')->store('pdfs', 'public');
        }

        $issue->update([
            'year'         => (int) $data['year'],
            'volume'       => $data['volume'] ?? null,
            'number'       => (int) $data['number'],
            'published_at' => $data['published_at'] ?? null,
            'pdf_path'     => $issue->pdf_path,
        ]);

        return redirect()->route('issues.dashboard')->with('success', 'Issue aktualizované.');
    }

    public function destroy(Issue $issue)
    {
        if ($issue->pdf_path) {
            Storage::disk('public')->delete($issue->pdf_path);
        }

        $issue->delete();

        return redirect()->route('issues.dashboard')->with('success', 'Issue vymazané.');
    }
}