<?php

namespace App\Services;

use App\Models\Projects;
use Illuminate\Http\Request;

class ProjectService
{
    /**
     * Handle filtering and limit logic for projects.
     */
    public function getFilteredProjects(Request $request)
    {
        $query = Projects::query()->latest();
        $filter = $request->get('filter');

        // Apply Filtering
        if ($request->filled('filter') && $filter !== 'all') {
            $filter = strtolower(trim($filter));

            // Escape LIKE metacharacters so user input cannot change the match pattern.
            $term = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $filter);

            $query->whereRaw('LOWER(tech_stack) LIKE ? ESCAPE ?', ['%' . $term . '%', '\\']);

            // If filtering, we usually want all matches
            return $query->get();
        }

        // Apply "Load More" or "Limit" logic
        if ($request->load_all === 'true') {
            return $query->get();
        }

        return $query->take(6)->get();
    }
}