@if ($paginator->hasPages())
  <nav class="flex items-center justify-between px-4 py-3 sm:px-6">
    {{-- Mobile view --}}
    <div class="flex flex-1 justify-between sm:hidden">
      @if ($paginator->onFirstPage())
        <span
          class="relative inline-flex cursor-default items-center rounded-md bg-black/20 px-4 py-2 text-sm font-medium text-gray-400">
          Previous
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}"
          class="relative inline-flex items-center rounded-md bg-black/20 px-4 py-2 text-sm font-medium text-white hover:bg-black/40">
          Previous
        </a>
      @endif

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
          class="relative ml-3 inline-flex items-center rounded-md bg-black/20 px-4 py-2 text-sm font-medium text-white hover:bg-black/40">
          Next
        </a>
      @else
        <span
          class="relative ml-3 inline-flex cursor-default items-center rounded-md bg-black/20 px-4 py-2 text-sm font-medium text-gray-400">
          Next
        </span>
      @endif
    </div>

    {{-- Desktop view --}}
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-400">
          Showing
          <span class="font-medium text-white">{{ $paginator->firstItem() ?? 0 }}</span>
          to
          <span class="font-medium text-white">{{ $paginator->lastItem() ?? 0 }}</span>
          of
          <span class="font-medium text-white">{{ $paginator->total() }}</span>
          results
        </p>
      </div>

      @if ($paginator->hasPages())
        <div>
          <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
              <span
                class="relative inline-flex cursor-default items-center rounded-l-md bg-black/20 px-3 py-2 text-gray-400">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" />
                </svg>
              </span>
            @else
              <a href="{{ $paginator->previousPageUrl() }}"
                class="relative inline-flex items-center rounded-l-md bg-black/20 px-3 py-2 text-white hover:bg-black/40">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" />
                </svg>
              </a>
            @endif

            {{-- Page Numbers --}}
            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
              @if ($i == $paginator->currentPage())
                <span
                  class="relative z-10 inline-flex cursor-default items-center bg-yns_yellow px-4 py-2 text-sm font-semibold text-black">
                  {{ $i }}
                </span>
              @else
                <a href="{{ $paginator->url($i) }}"
                  class="relative inline-flex items-center bg-black/20 px-4 py-2 text-sm font-semibold text-white hover:bg-black/40">
                  {{ $i }}
                </a>
              @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
              <a href="{{ $paginator->nextPageUrl() }}"
                class="relative inline-flex items-center rounded-r-md bg-black/20 px-3 py-2 text-white hover:bg-black/40">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" />
                </svg>
              </a>
            @else
              <span
                class="relative inline-flex cursor-default items-center rounded-r-md bg-black/20 px-3 py-2 text-gray-400">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" />
                </svg>
              </span>
            @endif
          </nav>
        </div>
      @endif
    </div>
  </nav>
@endif
