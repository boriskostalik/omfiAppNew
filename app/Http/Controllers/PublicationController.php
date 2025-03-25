<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Requests\PublicationRequest;
use Illuminate\Support\Facades\Auth;

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
        ]);
    }

    public function showForm(Publication $id) {
        $publication = Publication::with(['authors' => function ($query) {
            $query->orderBy('title', 'desc');
        }])->findOrFail($id);
    
        return Inertia::render('Dashboard/PublicationForm', [
            'publication' => $publication,
            'author' => $publication->author,
        ]);
    }

    public function store(PublicationRequest $request)
    {   
        
        $data = array_merge(
            $request->validated(),
        );
        Publication::create($data);

        return redirect()->route('publications.dashboard');
    }

    public function update(PublicationRequest $request, Publication $publication)
    {
        $publication->update($request->validated());

        return redirect()->route('publications.index');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();

        return redirect()->route('publications.index');
    }
}