@php
  $user = auth()->user();
  $links = [
      'finances' => $user->can('view_finances')
          ? route('admin.dashboard.show-finances', ['dashboardType' => $dashboardType])
          : null,
      'events' => $user->can('view_events')
          ? route('admin.dashboard.show-events', ['dashboardType' => $dashboardType])
          : null,
      'todo_list' => $user->can('view_todo_list')
          ? route('admin.dashboard.todo-list', ['dashboardType' => $dashboardType])
          : null,
      'reviews' => $user->can('view_reviews')
          ? route('admin.dashboard.get-reviews', [
              'filter' => 'all',
              'dashboardType' => $dashboardType,
          ])
          : null,
      'notes' => $user->can('view_notes')
          ? route('admin.dashboard.show-notes', ['dashboardType' => $dashboardType])
          : null,
      'documents' => $user->can('view_documents')
          ? route('admin.dashboard.documents.index', ['dashboardType' => $dashboardType])
          : null,
      'users' => $user->can('view_users') ? route('admin.dashboard.users', ['dashboardType' => $dashboardType]) : null,
      'jobs' => $user->can('view_jobs') ? route('admin.dashboard.jobs', ['dashboardType' => $dashboardType]) : null,
  ];
@endphp

<nav x-data="{ open: false }" class="bg-black">
  <div class="mx-auto max-w-[1920px] px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      {{-- Logo section --}}
      <div class="flex items-center">
        <a href="/" class="shrink-0">
          <x-application-logo class="block h-8 w-auto" />
        </a>
      </div>

      {{-- Desktop Navigation --}}
      <div class="hidden flex-1 lg:ml-8 lg:block">
        <div class="flex items-center">
          <div class="shrink-0">
            <a href="{{ route('dashboard.index') }}"
              class="nav-link {{ request()->routeIs('dashboard.index') ? 'text-yns_yellow border-b-yns_yellow' : 'text-white hover:text-yns_yellow hover:border-b-yns_yellow' }} inline-flex h-16 items-center border-b-2 border-white transition duration-150 ease-in-out">
              Dashboard
            </a>
          </div>

          {{-- Scrollable Navigation Items with max-width container --}}
          <div class="relative ml-4">
            <div class="max-w-[600px] xl:max-w-[800px] 2xl:max-w-[1000px]">
              <div class="no-scrollbar flex space-x-4 overflow-x-auto">
                @foreach ($links as $module => $url)
                  @if ($url && isset($modules[$module]))
                    @php
                      $isEnabled = $modules[$module]['is_enabled'];
                    @endphp

                    @if ($isEnabled)
                      <a href="{{ $url }}"
                        class="nav-link {{ request()->is('dashboard/*/' . $module . '*') ? 'text-yns_yellow border-b-yns_yellow' : 'text-white hover:text-yns_yellow hover:border-b-yns_yellow' }} inline-flex h-16 shrink-0 items-center whitespace-nowrap border-b-2 border-white transition duration-150 ease-in-out">
                        {{ ucfirst(str_replace('_', ' ', $module)) }}
                      </a>
                    @endif
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- User Dropdown --}}
      <div class="hidden sm:ml-6 sm:flex sm:items-center">
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button class="nav-link flex items-center text-white hover:text-yns_yellow">
              <span>{{ Auth::user()->first_name }}</span>
              <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit', ['dashboardType' => lcfirst($dashboardType), 'id' => Auth::user()->id])">
              {{ __('Profile') }}
            </x-dropdown-link>
            <x-dropdown-link :href="route('logout')"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              {{ __('Logout') }}
            </x-dropdown-link>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
              @csrf
            </form>
          </x-slot>
        </x-dropdown>
      </div>

      {{-- Mobile menu button --}}
      <button id="mobile-menu-button" class="text-white hover:text-yns_yellow lg:hidden">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>

  {{-- Mobile menu --}}
  <div id="mobile-menu" class="fixed inset-0 z-50 hidden">
    {{-- Overlay --}}
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 opacity-0 transition-opacity duration-300"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 w-64 -translate-x-full bg-black transition-transform duration-300 ease-in-out">
      <div class="border-b border-gray-800 p-4">
        <div class="flex items-center justify-between">
          <x-application-logo class="block h-8 w-auto" />
          <button id="close-mobile-menu" class="text-white hover:text-yns_yellow">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <nav class="space-y-1 px-2 pb-3 pt-2">
        @foreach ($links as $module => $url)
          @if ($url && isset($modules[$module]))
            @php
              $isEnabled = $modules[$module]['is_enabled'];
            @endphp

            @if ($isEnabled)
              <a href="{{ $url }}"
                class="mobile-nav-link {{ request()->is('dashboard/*/' . $module . '*') ? 'text-yns_yellow' : 'text-white' }}">
                {{ ucfirst(str_replace('_', ' ', $module)) }}
              </a>
            @endif
          @endif
        @endforeach
      </nav>
    </aside>
  </div>
</nav>

<style>

</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('mobile-menu-button');
    const closeButton = document.getElementById('close-mobile-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    const sidebar = mobileMenu.querySelector('aside');
    const overlay = document.getElementById('mobile-menu-overlay');

    function toggleMenu() {
      const isOpen = !mobileMenu.classList.contains('hidden');

      if (!isOpen) {
        // Open menu
        mobileMenu.classList.remove('hidden');
        requestAnimationFrame(() => {
          overlay.classList.remove('opacity-0');
          sidebar.classList.remove('-translate-x-full');
        });
      } else {
        // Close menu
        overlay.classList.add('opacity-0');
        sidebar.classList.add('-translate-x-full');
        setTimeout(() => {
          mobileMenu.classList.add('hidden');
        }, 300);
      }
    }

    menuButton.addEventListener('click', toggleMenu);
    closeButton.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    // Close on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
        toggleMenu();
      }
    });
  });
</script>
