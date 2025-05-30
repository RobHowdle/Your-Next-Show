@props([
    'showRoleModal' => false,
    'roles' => [],
])

<template x-teleport="body">
  <div x-show="showRoleModal" x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showRoleModal = false"></div>

    {{-- Modal Panel --}}
    <div class="relative flex min-h-screen items-center justify-center p-4">
      <div class="relative w-full max-w-2xl rounded-lg border border-gray-800 bg-gray-900 p-6" @click.stop
        x-transition:enter="transition duration-200" x-transition:enter-start="transform scale-95 opacity-0"
        x-transition:enter-end="transform scale-100 opacity-100" x-transition:leave="transition duration-200"
        x-transition:leave-start="transform scale-100 opacity-100"
        x-transition:leave-end="transform scale-95 opacity-0">

        {{-- Modal Content --}}
        <h3 class="mb-4 text-xl font-semibold text-white">Choose Your Account Type</h3>
        <p class="mb-6 text-gray-400">Select the option that best describes how you'll use Find My Venue</p>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          @foreach ($roles as $role)
            <button type="button" @click="selectRole('{{ $role['id'] }}', '{{ $role['name'] }}')"
              class="role-button flex flex-col items-center rounded-lg border border-gray-700 p-4 hover:bg-gray-800">
              {!! $role['icon'] !!}
              <span class="text-lg font-medium text-white">{{ $role['name'] }}</span>
              <span class="mt-2 text-center text-sm text-gray-400">{{ $role['description'] }}</span>
            </button>
          @endforeach
        </div>

        <div class="mt-6 flex justify-end">
          <button type="button" @click="showRoleModal = false"
            class="rounded-md border border-gray-600 px-4 py-2 text-sm text-gray-400 hover:bg-gray-800">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
