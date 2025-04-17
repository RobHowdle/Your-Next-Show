@php
  $user = auth()->user();
  $modules = $modules ?? [];

  $navigationLinks = [
      'finances' => [
          'permission' => 'view_finances',
          'route' => 'admin.dashboard.show-finances',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'events' => [
          'permission' => 'view_events',
          'route' => 'admin.dashboard.show-events',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'todo_list' => [
          'permission' => 'view_todo_list',
          'route' => 'admin.dashboard.todo-list',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'reviews' => [
          'permission' => 'view_reviews',
          'route' => 'dashboard.reviews',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'notes' => [
          'permission' => 'view_notes',
          'route' => 'admin.dashboard.notes',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'documents' => [
          'permission' => 'view_documents',
          'route' => 'admin.dashboard.documents.index',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'users' => [
          'permission' => 'view_users',
          'route' => 'admin.dashboard.users',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
      'jobs' => [
          'permission' => 'view_jobs',
          'route' => 'admin.dashboard.jobs',
          'params' => ['dashboardType' => $dashboardType ?? 'default'],
      ],
  ];

  // Build active links based on permissions
  $links = collect($navigationLinks)
      ->map(function ($config, $module) use ($user, $modules) {
          if (!$user->can($config['permission']) || !isset($modules[$module])) {
              return null;
          }
          return route($config['route'], $config['params']);
      })
      ->filter()
      ->toArray();
@endphp

<nav x-data="{ open: false }" class="border-b border-yns_black bg-yns_black backdrop-blur-sm">
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
              class="nav-link {{ request()->routeIs('dashboard.index') ? 'border-b-yns_yellow text-yns_yellow' : 'border-transparent text-gray-400 hover:border-gray-700 hover:text-gray-200' }} inline-flex h-16 items-center border-b-2 px-4 text-sm font-medium transition duration-150 ease-in-out">
              Dashboard
            </a>
          </div>

          <div class="relative ml-4">
            <div class="max-w-[600px] xl:max-w-[800px] 2xl:max-w-[1000px]">
              <div class="no-scrollbar flex space-x-1 overflow-x-auto">
                @foreach ($links as $module => $url)
                  @if (isset($modules[$module]) && $modules[$module]['is_enabled'])
                    @php
                      $isActive = request()->is('dashboard/*/' . $module . '*');
                    @endphp

                    <a href="{{ $url }}"
                      class="nav-link {{ $isActive ? 'border-b-yns_yellow bg-gray-800 text-yns_yellow' : 'border-transparent text-gray-400 hover:border-gray-700 hover:bg-gray-800/50 hover:text-gray-200' }} inline-flex h-16 shrink-0 items-center whitespace-nowrap border-b-2 px-4 text-sm font-medium transition duration-150 ease-in-out">
                      {{ ucfirst(str_replace('_', ' ', $module)) }}
                    </a>
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
            <button
              class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 transition hover:bg-gray-800 hover:text-gray-200">
              <span>{{ Auth::user()->first_name }}</span>
              <svg class="ml-2 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            <div class="rounded-lg bg-gray-800 p-1 shadow-xl ring-1 ring-gray-700" style="z-index: 100;">
              <x-dropdown-link :href="route('profile.edit', ['dashboardType' => lcfirst($dashboardType), 'id' => Auth::user()->id])" class="rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                {{ __('Profile') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('logout')" class="rounded-md text-gray-300 hover:bg-gray-700 hover:text-white"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
              </x-dropdown-link>
            </div>
          </x-slot>
        </x-dropdown>
      </div>

      {{-- Mobile menu button --}}
      <button id="mobile-menu-button"
        class="rounded-lg p-2 text-gray-400 hover:bg-gray-800 hover:text-gray-200 lg:hidden">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>

  {{-- Mobile menu --}}
  <div id="mobile-menu" class="fixed inset-0 z-50 hidden">
    {{-- Overlay --}}
    <div id="mobile-menu-overlay"
      class="fixed inset-0 bg-black/80 opacity-0 backdrop-blur-sm transition-opacity duration-300"></div>

    {{-- Sidebar --}}
    <aside
      class="fixed inset-y-0 left-0 w-72 -translate-x-full bg-gray-900 shadow-xl transition-transform duration-300 ease-in-out">
      <div class="border-b border-gray-800 p-4">
        <div class="flex items-center justify-between">
          <x-application-logo class="block h-8 w-auto" />
          <button id="close-mobile-menu" class="rounded-lg p-2 text-gray-400 hover:bg-gray-800 hover:text-gray-200">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <nav class="space-y-1 p-2">
        @foreach ($links as $module => $url)
          @if ($url && isset($modules[$module]))
            @php
              $isEnabled = $modules[$module]['is_enabled'];
              $isActive = request()->is('dashboard/*/' . $module . '*');
            @endphp

            @if ($isEnabled)
              <a href="{{ $url }}"
                class="mobile-nav-link {{ $isActive ? 'bg-gray-800 text-yns_yellow' : 'text-gray-400 hover:bg-gray-800 hover:text-gray-200' }} group flex items-center rounded-lg px-4 py-2.5 text-sm font-medium">
                {{ ucfirst(str_replace('_', ' ', $module)) }}
              </a>
            @endif
          @endif
        @endforeach
      </nav>
    </aside>
  </div>
  <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
    @csrf
  </form>
</nav>
<style>
  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }

  .nav-link {
    position: relative;
  }

  .nav-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    transform: scaleX(0);
    transition: transform 0.2s ease;
  }

  .nav-link:hover::after {
    transform: scaleX(1);
  }

  @keyframes slideIn {
    from {
      transform: translateX(-100%);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  .mobile-nav-link {
    animation: slideIn 0.3s ease-out forwards;
    animation-delay: calc(var(--index) * 0.1s);
    opacity: 0;
  }
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
