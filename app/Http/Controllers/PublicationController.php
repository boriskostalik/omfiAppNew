<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;
use Illuminate\Http\Request;

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
}