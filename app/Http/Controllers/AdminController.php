<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Venue;
use App\Models\Promoter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function getVenues()
    {
        $venueCount = Venue::whereNull('deleted_at')->count();
        $locationCount = Venue::whereNull('deleted_at')->distinct('postal_town')->count();

        $genreList = file_get_contents(storage_path('app/public/text/genre_list.json'));
        $data = json_decode($genreList, true);

        $genres = $data['genres'];

        return view('admin.venues', compact('venueCount', 'locationCount', 'genres'));
    }

public function saveNewVenue(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'floating_name' => 'required|string',
                'address-input' => 'required',
                'postal-town-input' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'floating_capacity' => 'required|numeric',
                'floating_in_house_gear' => 'required|string',
                'floating_band_type' => 'required',
                'genres' => 'required|array',
                'floating_contact_name' => 'required|string',
                'floating_contact_number' => 'nullable|numeric|digits:11',
                'floating_contact_email' => 'nullable|email',
                'floating_contact_links' => 'nullable',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Venue::create([
                'name' => $request->input('floating_name'),
                'location' => $request->input('address-input'),
                'postal_town' => $request->input('postal-town-input'),
                'longitude' => $request->input('latitude'),
                'latitude' => $request->input('longitude'),
                'capacity' => $request->input('floating_capacity'),
                'in_house_gear' => $request->input('floating_in_house_gear'),
                'band_type' => $request->input('floating_band_type'),
                'genre' => json_encode($request->input('genres')),
                'contact_name' => $request->input('floating_contact_name'),
                'contact_number' => $request->input('floating_contact_number'),
                'contact_email' => $request->input('floating_contact_email'),
                'contact_link' => $request->input('floating_contact_links'),
            ]);

            return back()->with('success', 'Venue created successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error saving new venue: ' . $e->getMessage());

            // Optionally, you can return an error response or redirect to an error page
            return back()->with('error', 'An error occurred while saving the venue. Please try again later.')->withInput();
        }
    }

    public function getPromoters()
    {
        $promoterCount = Promoter::whereNull('deleted_at')->count();

        return view('admin.promoters', compact('promoterCount'));
    }

    public function saveNewPromoter(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'promoter_name' => 'required|string',
                'promoter_location' => 'required|string',
                'promoter_logo' => 'required|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
                'promoter_about_me' => 'required',
                'promoter_my_venues' => 'required',
                'promoter_contact_email' => 'required|email',
                'promoter_contact_number' => 'nullable|numeric|digits:11',
                'promoter_links' => 'nullable',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            // Get the uploaded image file
            $promoterLogoFile = $request->file('promoter_logo');

            // Generate a unique filename based on the promoter's name and extension
            $promoterName = $request->input('promoter_name');
            $promoterLogoExtension = $promoterLogoFile->getClientOriginalExtension();
            $promoterLogoFilename = Str::slug($promoterName) . '.' . $promoterLogoExtension;

            // Store the uploaded image in the storage directory
            $promoterLogoPath = $promoterLogoFile->storeAs('public/images', $promoterLogoFilename);

            Promoter::create([
                'name' => $request->input('promoter_name'),
                'location' => $request->input('promoter_location'),
                'logo_url' => $promoterLogoPath,
                'about_me' => $request->input('promoter_about_me'),
                'my_venues' => $request->input('promoter_my_venues'),
                'contact_number' => $request->input('promoter_contact_number'),
                'contact_email' => $request->input('promoter_contact_email'),
                'contact_link' => $request->input('promoter_contact_links'),
            ]);

            return back()->with('success', 'Promoter created successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error saving new venue: ' . $e->getMessage());

            // Optionally, you can return an error response or redirect to an error page
            return back()->with('error', 'An error occurred while saving the promter. Please try again later.')->withInput();
        }
    }
}