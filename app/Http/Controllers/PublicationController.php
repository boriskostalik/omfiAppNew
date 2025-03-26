<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Requests\PublicationRequest;
use App\Models\Author;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        // Filter publications if a search query exists
        $query = Publication::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search by publication title
                $q->where('title', 'like', "%{$search}%")
                //   Search by author name through the pivot table
                    ->orWhereHas('authors', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%");
                    });
            });
        }
        
        $publications = $query->with('authors')->paginate($perPage);

        return Inertia::render('PublicationsPage', [
            'publications' => $publications,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }

    public function detail($id)
    {
        // Retrieve the publication with its authors, ordered by title in descending order
        $publication = Publication::with(['authors' => function ($query) {
            $query->orderBy('firstname', 'desc');
        }])->findOrFail($id);

        // Return view with the publication data
        return Inertia::render('PublicationDetailPage', [
            'publication' => $publication,
        ]);
    }

    public function indexDashboard(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $sortField = $request->input('sortField', 'title');
        $sortOrder = $request->input('sortOrder', 'asc');
    
        $filters = $request->only(['title', 'type', 'year', 'journal']);
    
        $query = Publication::query()->with('authors');
        $authors = Author::query();
    
        // Apply search filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%") // Move this line out of orWhereHas
                  ->orWhereHas('authors', function ($q) use ($search) {
                      $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%");
                  });
            });
        }
    
        foreach ($filters as $field => $value) {
            if ($value) {
                $query->where($field, 'like', "%{$value}%");
            }
        }
    
        // Apply sorting
        if (in_array($sortField, ['title', 'type', 'year', 'journal'])) {
            $query->orderBy($sortField, $sortOrder);
        }
    
        $publications = $query->paginate($perPage);
    
        return Inertia::render('Dashboard/Publications', [
            'publications' => $publications,
            'per_page' => $perPage,
            'search' => $search,
            'filters' => $filters, // Pass the filters back
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'entered_by' => Auth::user()->id,
            'authors' => $authors->get(),
        ]);
    }


    public function store(PublicationRequest $request)
    {   
        $data = $request->validated();

        // Create the publication
        $publication = Publication::create($data);

        // Ensure authors exist in the request
        if ($request->has('authors') && is_array($request->authors)) {
            $authorsData = [];

            foreach ($request->authors as $index => $author) {
                // Check if it's an array/object or a simple ID
                $authorId = is_array($author) ? $author['id'] : $author; 

                $authorsData[$authorId] = [
                    'rank' => $index + 1,
                    'is_editor' => 'N'
                ];
            }

            // Attach authors using the pivot table
            $publication->authors()->attach($authorsData);
        }

        return redirect()->route('publications.dashboard');
    }

    public function update(PublicationRequest $request, Publication $publication)
    {   
        $data = $request->validated();
    
        // Update the publication
        $publication->update($data);
    
        // Ensure authors exist in the request
        if ($request->has('authors') && is_array($request->authors)) {
            $authorsData = [];
    
            foreach ($request->authors as $index => $author) {
                // Handle both object format and simple ID format
                $authorId = is_array($author) ? $author['id'] : $author;
    
                $authorsData[$authorId] = [
                    'rank' => $index + 1,
                    'is_editor' => 'N'
                ];
            }
    
            // Sync authors (removes old ones, adds new ones)
            $publication->authors()->sync($authorsData);
        }
    
        return redirect()->route('publications.dashboard');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();

        return redirect()->route('publications.index');
    }
}