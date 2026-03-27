<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $perPage   = (int) $request->input('per_page', 20);
        $search    = $request->input('search');
        $sortField = $request->input('sortField', 'year');
        $sortOrder = $request->input('sortOrder', 'desc');

        $query = Issue::query()->withCount('publications');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('year', 'like', "%{$search}%")
                  ->orWhere('volume', 'like', "%{$search}%");
            });
        }

        $allowed = ['year', 'number', 'volume'];
        if (in_array($sortField, $allowed, true)) {
            $query->orderBy($sortField, $sortOrder === 'asc' ? 'asc' : 'desc');
            if ($sortField === 'year') {
                $query->orderBy('number', 'asc');
            }
        } else {
            $query->orderByDesc('year')->orderBy('number', 'asc');
        }

        $issues = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('Dashboard/Issues', [
            'issues'    => $issues,
            'search'    => $search,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'year'         => 'required|numeric',
            'volume'       => 'nullable|string|max:16',
            'number'       => ['required', 'numeric', \Illuminate\Validation\Rule::unique('issues')->where(fn ($q) => $q->where('year', $request->year))],
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
            'number'       => ['required', 'numeric', \Illuminate\Validation\Rule::unique('issues')->where(fn ($q) => $q->where('year', $request->year))->ignore($issue->id)],
            'published_at' => 'nullable|date',
            'pdf'          => 'nullable|file|mimes:pdf|max:20480',
            'remove_pdf'   => 'nullable|string',
        ]);
        if ($request->input('remove_pdf') === 'true' && $issue->pdf_path) {
            Storage::disk('public')->delete($issue->pdf_path);
            $issue->pdf_path = null;
        }
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