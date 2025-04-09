<div id="opportunityModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
  aria-modal="true">
  {{-- Fixed backdrop --}}
  <div class="fixed inset-0 bg-black/70 transition-opacity duration-300 ease-in-out"></div>

  {{-- Modal container --}}
  <div class="fixed inset-0 z-[101] overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
      {{-- Modal content --}}
      <div
        class="modal-content relative w-full max-w-lg transform overflow-hidden rounded-lg bg-yns_dark_gray text-left shadow-xl transition-all">
        {{-- Modal header --}}
        <div class="border-b border-gray-700 bg-gray-900/50 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Create New Opportunity</h3>
            <button type="button"
              class="close-modal rounded-full p-2 text-gray-400 hover:bg-gray-800 hover:text-white">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        {{-- Modal body --}}
        <div class="max-h-[70vh] overflow-y-auto px-6 py-4">
          <form id="opportunityForm" class="space-y-6">
            {{-- Hidden Fields --}}
            <input type="hidden" name="set_length" id="set_length">

            {{-- Opportunity Type Selection --}}
            <div class="space-y-2">
              <label class="block font-medium">What are you looking for?</label>
              <select name="type" id="opportunityType" class="mt-1 w-full rounded-md border-gray-700 bg-gray-800"
                required>
                <option value="">Select type...</option>
                <option value="artist_wanted">Artist</option>
                <option value="venue_wanted">Venue</option>
                <option value="promoter_wanted">Promoter</option>
                <option value="photographer_wanted">Photographer</option>
                <option value="designer_wanted">Designer</option>
                <option value="videographer_wanted">Videographer</option>
              </select>
            </div>

            {{-- Dynamic Fields Container --}}
            <div id="dynamicFields" class="space-y-6">
              {{-- Fields will be loaded dynamically based on type --}}
            </div>

            {{-- Common Fields --}}
            <div class="space-y-6">
              {{-- Main Genres --}}
              <div class="space-y-2">
                <div id="mainGenresContainer">
                  {{-- Will be populated from event genres --}}
                </div>
              </div>

              {{-- Poster Selection --}}
              <div class="space-y-2">
                <label class="block font-medium">Poster</label>
                <div class="flex items-center space-x-4">
                  <label class="flex items-center space-x-2">
                    <input type="radio" name="poster_type" value="event" checked>
                    <span>Use Event Poster</span>
                  </label>
                  <label class="flex items-center space-x-2">
                    <input type="radio" name="poster_type" value="custom">
                    <span>Upload Custom Poster</span>
                  </label>
                </div>
                <div id="customPosterUpload" class="mt-2 hidden">
                  <input type="file" name="custom_poster" accept="image/*" class="w-full">
                </div>
              </div>

              {{-- Additional Requirements --}}
              <div>
                <label class="block font-medium">Additional Requirements</label>
                <textarea name="additional_requirements" rows="3" class="mt-1 w-full rounded-md border-gray-700 bg-gray-800"></textarea>
              </div>
            </div>

            {{-- Modal footer --}}
            <div class="border-t border-gray-700 bg-gray-900/50 px-6 py-4">
              <div class="flex justify-end space-x-3">
                <button type="button"
                  class="close-modal rounded-lg px-4 py-2 text-gray-400 hover:bg-gray-800 hover:text-white">
                  Cancel
                </button>
                <button type="button" id="createListingBtn"
                  class="rounded-lg bg-yns_yellow px-4 py-2 font-semibold text-black transition-colors hover:bg-yns_yellow/90">
                  Create Listing
                </button>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  <style>
    /* Modal animations and styling */
    #opportunityModal {
      transition: visibility 0s, opacity 0.3s ease-in-out;
    }

    #opportunityModal.hidden {
      visibility: hidden;
      opacity: 0;
    }

    #opportunityModal:not(.hidden) {
      visibility: visible;
      opacity: 1;
    }

    /* Modal content animation */
    #opportunityModal.hidden .modal-content {
      transform: scale(0.95) translateY(-10px);
      opacity: 0;
    }

    #opportunityModal:not(.hidden) .modal-content {
      transform: scale(1) translateY(0);
      opacity: 1;
    }

    /* Prevent body scroll when modal is open */
    body.modal-open {
      overflow: hidden;
    }
  </style>
