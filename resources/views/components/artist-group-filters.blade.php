<!-- Band Type Filter -->
<div class="rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
  <h3 class="flex items-center justify-between font-heading text-lg font-bold text-white">
    Band Type
    <span class="fas fa-chevron-down text-sm md:hidden"></span>
  </h3>
  <div class="filter-content mt-4 hidden md:block">
    <div class="max-h-[300px] overflow-y-auto pr-2 md:max-h-[120px] lg:max-h-[300px]">
      <div class="grid grid-cols-1 gap-y-2">
        @foreach ($bandTypes as $type)
          <label class="flex items-center gap-2 py-1 text-gray-300">
            <input type="checkbox" name="band_types[]" value="{{ $type }}"
              class="filter-checkbox-locations rounded border-gray-600 bg-gray-700 text-yns_yellow">
            <span class="truncate">{{ Str::title(str_replace('-', ' ', $type)) }}</span>
          </label>
        @endforeach
      </div>
    </div>
  </div>
</div>
