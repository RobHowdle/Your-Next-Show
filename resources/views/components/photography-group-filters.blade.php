<div class="rounded-lg border border-gray-700 bg-black/50 p-4 backdrop-blur-sm">
  <h3 class="flex items-center justify-between font-heading text-lg font-bold text-white">
    Environment Types
    <span class="fas fa-chevron-down text-sm md:hidden"></span>
  </h3>
  <div class="filter-content mt-4 hidden md:block">
    @foreach ($photographyEnvironments as $category => $environments)
      <div class="max-h-[300px] overflow-y-auto pr-2 md:max-h-[120px] lg:max-h-[300px]">
        <h4 class="mb-2 mt-4 text-sm font-bold text-gray-400">{{ $category }}</h4>
        <div class="grid grid-cols-2 gap-y-2">
          @foreach ($environments as $environment)
            <label class="flex items-center gap-2 py-1 text-gray-300">
              <input type="checkbox" name="photography_filters[{{ $category }}][]" value="{{ $environment }}"
                class="environment-checkbox rounded border-gray-600 bg-gray-700 text-yns_yellow">
              <span
                class="truncate">{{ Str::title(str_replace('-', ' ', $environment['name'] ?? $environment)) }}</span>
            </label>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</div>
