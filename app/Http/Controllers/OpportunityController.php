<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpportunityController extends Controller
{
    /**
     * Controller for displaying the various opps for wanted ads ie venues wanting bands, promoters wanting venues, photographers wanting bands, etc.
     * @return \Illuminate\Http\Response
     */

    public function getTypeFields(Request $request, $dashboardType, $opportunityType)
    {
        // Get selected genres from the form
        $selectedGenres = $request->input('genres', []);

        // \Log::info('Selected genres:', $selectedGenres);

        // Load and parse the genre list
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];

        // Create a structured array of selected genres with their subgenres
        $genresWithSubgenres = [];
        foreach ($data['genres'] ?? [] as $genre) {
            if (in_array($genre['name'], $selectedGenres)) {
                $genresWithSubgenres[$genre['name']] = $genre['subgenres'] ?? [];
            }
        }

        $formData = [
            'genres' => $selectedGenres,
            'genres_with_subgenres' => $genresWithSubgenres,
            'event_date' => $request->input('event_date'),
            'event_start_time' => $request->input('event_start_time'),
            'event_end_time' => $request->input('event_end_time'),
            'venue_id' => $request->input('venue_id'),
            'headliner_id' => $request->input('headliner_id'),
            'main_support_id' => $request->input('main_support_id'),
            'opener_id' => $request->input('opener_id'),
            'bands_ids' => $request->input('bands_ids'),
        ];

        $html = match ($opportunityType) {
            'artist_wanted' => view('components.opportunities.types.artist-wanted', [
                'formData' => $formData,
                'genresWithSubgenres' => $genresWithSubgenres,
            ])->render(),
            'venue_wanted' => view('components.opportunities.types.venue-wanted', [
                'formData' => $formData,
                'genresWithSubgenres' => $genresWithSubgenres,
            ])->render(),
            'promoter_wanted' => view('components.opportunities.types.promoter-wanted', [
                'formData' => $formData,
                'genresWithSubgenres' => $genresWithSubgenres,
            ])->render(),
            'photographer_wanted' => view('components.opportunities.types.photographer-wanted', [
                'formData' => $formData,
                'genresWithSubgenres' => $genresWithSubgenres,
            ])->render(),
            'designer_wanted' => view('components.opportunities.types.designer-wanted', [
                'formData' => $formData,
                'genresWithSubgenres' => $genresWithSubgenres,
            ])->render(),
            'videographer_wanted' => view('components.opportunities.types.videographer-wanted', [
                'formData' => $formData,
                'genresWithSubgenres' => $genresWithSubgenres,
            ])->render(),
            default => ''
        };

        return response()->json(['html' => $html]);
    }

    public function index()
    {
        // This is the main page for the opportunities
        return view('opportunities');
    }

    public function createEventOpportunity(array $data)
    {
        try {
            // Validate opportunity data
            $validated = Validator::make($data, [
                'serviceable_type' => 'required|string',
                'serviceable_id' => 'required',
                'related_type' => 'required|string',
                'related_id' => 'required|exists:events,id',
                'type' => 'required|string',
                'position_type' => 'required|string',
                'genres' => 'required|array',
                'title' => 'required|string|max:255',
                'performance_start_time' => 'required_if:type,artist_wanted',
                'performance_end_time' => 'required_if:type,artist_wanted',
                'set_length' => 'required_if:type,artist_wanted',
                'application_deadline' => 'required|date',
                'poster_type' => 'required|in:event,custom',
                'poster_url' => 'nullable|string',
                'additional_requirements' => 'nullable|string',
                'excluded_entities' => 'nullable|array',
            ])->validate();

            // Create the opportunity
            $opportunity = Opportunity::create([
                'serviceable_type' => $validated['serviceable_type'],
                'serviceable_id' => $validated['serviceable_id'],
                'related_type' => $validated['related_type'],
                'related_id' => $validated['related_id'],
                'title' => $validated['title'],
                'additional_info' => $validated['additional_requirements'] ?? null,
                'type' => $validated['type'],
                'position_type' => $validated['position_type'],
                'status' => 'open',
                'use_related_poster' => $validated['poster_type'] === 'event',
                'poster_url' => $validated['poster_type'] === 'event' ? $validated['poster_url'] : null,
                'start_time' => $validated['performance_start_time'] ?? null,
                'end_time' => $validated['performance_end_time'] ?? null,
                'set_length' => $validated['set_length'] ?? null,
                'application_deadline' => $validated['application_deadline'] ?? null,
                'genres' => json_encode($validated['genres'] ?? []),
                'excluded_entities' => json_encode($validated['excluded_entities'] ?? []),
            ]);

            return $opportunity;
        } catch (\Exception $e) {

            \Log::error('Error creating opportunity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}