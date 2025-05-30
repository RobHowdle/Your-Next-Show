@php
  use Illuminate\Support\Facades\Route;
@endphp
<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  {{-- Main Container with Background --}}
  <div class="relative min-h-screen">

    <div class="relative mx-auto w-full max-w-screen-2xl py-8">
      <div class="px-4">
        {{-- Header Section --}}
        <div class="relative mb-8">
          {{-- Background with overlay --}}
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-gray-900 via-black to-gray-900 opacity-75"></div>

          {{-- Content --}}
          <div class="relative px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
              <h1 class="font-heading text-3xl font-bold text-white md:text-4xl">
                New Event
              </h1>
              <p class="mt-2 text-gray-400">Add your event details and configuration</p>
            </div>
          </div>
        </div>

        {{-- Main Form Container --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-md backdrop-saturate-150">
          <form id="eventForm" method="POST" enctype="multipart/form-data" data-dashboard-type="{{ $dashboardType }}">
            @csrf
            <input type="hidden" name="pending_opportunities" value="[]" id="pending_opportunities">

            <input type="hidden" id="dashboard_type" value="{{ $dashboardType }}">
            @if ($profileData['hasMultiplePlatforms'])
              <div class="group mb-4">
                <x-input-label-dark>Ticket Platform</x-input-label-dark>
                <select id="ticket_platform" name="ticket_platform"
                  class="focus:border-yns_pink rounded-md border-gray-300 bg-gray-700 text-white">
                  <option value="">Select Platform</option>
                  @foreach ($profileData['apiKeys'] as $apiKey)
                    <option value="{{ $apiKey['name'] }}">{{ $apiKey['display_name'] }}</option>
                  @endforeach
                </select>
              </div>
            @elseif($profileData['singlePlatform'])
              <div class="group mb-4">
                <input type="hidden" id="ticket_platform" name="ticket_platform"
                  value="{{ $profileData['singlePlatform']['name'] }}">
                <button type="button" id="importEventButton"
                  class="inline-flex items-center rounded-lg bg-opac_8_black px-6 py-3 font-heading text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-black/70">
                  <i class="fa-solid fa-download mr-2"></i>
                  Import from {{ $profileData['singlePlatform']['display_name'] }}
                </button>
              </div>
            @endif

            {{-- Add these hidden fields --}}
            <input type="hidden" id="platform_event_id" name="platform_event_id">
            <input type="hidden" id="platform_event_url" name="platform_event_url">

            {{-- Event source display --}}
            <div id="eventSource" class="mb-4 hidden text-sm text-gray-400">
              Event imported from <span id="platformName"></span>
            </div>

            <div class="grid gap-8 p-6 lg:grid-cols-2 lg:p-8">
              {{-- Left Column: Core Event Information --}}
              <div class="space-y-6">
                {{-- Basic Event Details --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Information</h2>
                  <div class="space-y-6">
                    {{-- Event Name --}}
                    <div>
                      <x-input-label-dark :required="true">Event Name</x-input-label-dark>
                      <x-text-input id="event_name" name="event_name" :required="true"
                        class="mt-1 block w-full"></x-text-input>
                    </div>

                    {{-- Date and Time Section --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                      <div class="col-span-2">
                        <x-input-label-dark :required="true">Event Date</x-input-label-dark>
                        <x-date-input id="event_date" name="event_date" :required="true"
                          class="mt-1 block w-full"></x-date-input>
                      </div>
                      <div>
                        <x-input-label-dark :required="true">Start Time</x-input-label-dark>
                        <x-time-input id="event_start_time" name="event_start_time" :required="true"
                          class="mt-1 block w-full"></x-time-input>
                      </div>
                      <div>
                        <x-input-label-dark>End Time</x-input-label-dark>
                        <x-time-input id="event_end_time" name="event_end_time"
                          class="mt-1 block w-full"></x-time-input>
                      </div>
                      <div class="col-span-2">
                        <x-input-label-dark :required="true">Event Genres</x-input-label-dark>
                        <div class="mt-2 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                          @foreach ($genres as $genre)
                            <label class="flex items-center space-x-2">
                              <input type="checkbox" value="{{ $genre }}" name="genres[]"
                                @if (in_array($genre, old('genres', $event->genres ?? []))) checked @endif
                                class="rounded border-gray-700 bg-gray-800 text-yns_yellow focus:ring-yns_yellow">
                              <span class="text-sm text-white">{{ $genre }}</span>
                            </label>
                          @endforeach
                        </div>
                      </div>
                    </div>

                    {{-- Description --}}
                    <div>
                      <x-input-label-dark :required="true">Description</x-input-label-dark>
                      <x-textarea-input id="event_description" name="event_description" :required="true"
                        class="mt-1 block w-full" rows="4"></x-textarea-input>
                    </div>
                  </div>
                </div>

                {{-- Venue Section --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Venue Details</h2>
                  <div>
                    <x-input-label-dark :required="true">Venue Name</x-input-label-dark>
                    <x-text-input id="venue_name" name="venue_name" autocomplete="off" :required="true"
                      class="mt-1 block w-full"></x-text-input>
                    <ul id="venue-suggestions"
                      class="absolute z-10 mt-1 hidden w-auto rounded-lg border border-gray-700 bg-gray-800"></ul>
                    <x-text-input id="venue_id" name="venue_id" :required="true" type="hidden"></x-text-input>
                  </div>
                </div>

                {{-- Pricing & Links --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Tickets & Links</h2>
                  <div class="space-y-6">
                    <div>
                      <x-input-label-dark>Door Price (£)</x-input-label-dark>
                      <x-number-input-pound id="on_the_door_ticket_price" name="on_the_door_ticket_price"
                        class="mt-1 block w-full" />
                    </div>
                    <div>
                      <x-input-label-dark>Pre-sale Link</x-input-label-dark>
                      <x-text-input id="ticket_url" name="ticket_url" type="url" placeholder="https://"
                        class="mt-1 block w-full"></x-text-input>
                    </div>
                    <div>
                      <x-input-label-dark>Facebook Event</x-input-label-dark>
                      <x-text-input id="facebook_event_url" name="facebook_event_url" type="url"
                        placeholder="https://facebook.com/events/" class="mt-1 block w-full"></x-text-input>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Right Column: Media and Lineup --}}
              <div class="space-y-6">
                {{-- Event Poster --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Poster <span
                      class="text-yns_red">*</span>
                  </h2>
                  <div class="space-y-4">
                    <img id="posterPreview" src="#" alt="Event Poster"
                      class="mb-4 hidden h-auto w-full rounded-lg border border-gray-800 object-cover">
                    <x-input-file id="poster_url" name="poster_url" accept="image/*"
                      class="mt-1 block w-full"></x-input-file>
                  </div>
                </div>

                {{-- Promoter Section --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Promoter</h2>
                  <div class="space-y-4">
                    <div>
                      <x-input-label-dark>Promoter Name</x-input-label-dark>
                      <x-text-input id="promoter_name" name="promoter_name" autocomplete="off"
                        placeholder="Type promoter name and press Enter" class="mt-1 block w-full"
                        value="{{ $serviceData['promoter_name'] ?? '' }}"></x-text-input>
                      <ul id="promoter-suggestions"
                        class="absolute z-10 mt-1 hidden w-auto rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-input-label-dark class="hidden">Promoter ID</x-input-label-dark>
                      <x-text-input id="promoter_ids" name="promoter_ids"
                        value="{{ $serviceData['promoter_id'] ?? '' }}" type="hidden"
                        class="mt-1 w-full"></x-text-input>
                    </div>
                  </div>
                </div>

                {{-- Event Lineup --}}
                <div class="rounded-lg border border-gray-800 bg-black/50 p-6">
                  <h2 class="mb-6 font-heading text-xl font-bold text-white">Event Lineup</h2>
                  <div class="space-y-6">
                    {{-- Headliner --}}
                    <div>
                      <x-input-label-dark :required="true">Headliner</x-input-label-dark>
                      <x-text-input id="headliner-search" name="headliner" autocomplete="off" :required="true"
                        class="mt-1 block w-full"></x-text-input>
                      <ul id="headliner-suggestions"
                        class="absolute z-10 mt-1 hidden w-auto rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="headliner_id" name="headliner_id" :required="true"
                        class="hidden"></x-text-input>
                    </div>

                    {{-- Support Acts --}}
                    <div class="grid gap-6 sm:grid-cols-2">
                      <div>
                        <x-input-label-dark>Main Support</x-input-label-dark>
                        <x-text-input id="main-support-search" name="main_support" autocomplete="off"
                          class="mt-1 block w-full"></x-text-input>
                        <ul id="main-support-suggestions"
                          class="absolute z-10 mt-1 hidden w-auto rounded-lg border border-gray-700 bg-gray-800">
                        </ul>
                        <x-text-input id="main_support_id" name="main_support_id" class="hidden"></x-text-input>
                      </div>

                      {{-- Additional Bands --}}
                      <div>
                        <x-input-label-dark>Additional Support Acts</x-input-label-dark>
                        <x-text-input id="bands-search" name="bands" class="band-input" autocomplete="off"
                          placeholder="Type band name and press Enter" class="mt-1 block w-full"></x-text-input>
                        <ul id="bands-suggestions"
                          class="absolute z-10 mt-1 hidden w-auto rounded-lg border border-gray-700 bg-gray-800">
                        </ul>
                        <x-text-input id="bands_ids" name="bands_ids" class="hidden"></x-text-input>
                      </div>
                    </div>

                    {{-- Opening Act --}}
                    <div>
                      <x-input-label-dark>Opening Act</x-input-label-dark>
                      <x-text-input id="opener-search" name="opener" autocomplete="off"
                        class="mt-1 block w-full"></x-text-input>
                      <ul id="opener-suggestions"
                        class="absolute z-10 mt-1 hidden w-auto rounded-lg border border-gray-700 bg-gray-800"></ul>
                      <x-text-input id="opener_id" name="opener_id" class="hidden"></x-text-input>
                    </div>
                  </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                  <button type="button" id="createOpportunityBtn"
                    class="inline-flex items-center rounded-lg border border-gray-700 bg-gray-800 px-6 py-3 font-heading text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-gray-700">
                    <i class="fas fa-bullhorn mr-2"></i>
                    Create Listing
                  </button>

                  <button type="submit"
                    class="inline-flex items-center rounded-lg bg-yns_yellow px-6 py-3 font-heading text-sm font-semibold text-black transition duration-150 ease-in-out hover:bg-yns_yellow/90">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                  </button>
                </div>
              </div>
            </div>
            <div id="platformEventModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
              <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                  <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                  class="inline-block transform overflow-hidden rounded-lg bg-yns_dark_gray text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                  <div class="bg-yns_dark_gray px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                      <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-white">Link Platform Event</h3>
                        <div class="mt-4">
                          <input type="text" id="platformEventSearch"
                            class="w-full rounded-md border-gray-600 bg-gray-700 text-white"
                            placeholder="Search events...">
                        </div>
                        <div id="platformEventResults" class="max-h-60 mt-4 overflow-y-auto"></div>
                      </div>
                    </div>
                  </div>
                  <div class="bg-yns_dark_gray px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button"
                      class="close-modal rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
                      Close
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
<x-modals.opportunity-modal :dashboardType="$dashboardType" />
@vite(['resources/js/app.js'])
