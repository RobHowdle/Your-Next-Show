<h1 class="mb-4 font-heading text-2xl font-bold text-white sm:text-3xl">
  {{ $greeting }}, <span class="text-indigo-400">{{ $userName }}</span>
</h1>

@if ($associatedEntity)
  <p class="mb-4 font-heading text-lg text-gray-400">
    Showing data for: <span class="text-white">{{ $associatedEntity }}</span>
  </p>
@endif
