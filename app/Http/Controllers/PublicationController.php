<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Publication;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::all();

        return Inertia::render('PublicationsPage', [
            'publications' => $publications
        ]);
    }
}
