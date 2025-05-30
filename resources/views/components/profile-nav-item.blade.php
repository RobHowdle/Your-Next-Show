@props(['icon', 'label', 'tab'])

<button @click="activeTab = '{{ $tab }}'"
  class="group flex items-center gap-3 rounded-lg px-4 py-2 transition-all duration-300"
  :class="activeTab === '{{ $tab }}'
      ?
      'bg-yns_orange text-white' :
      'text-gray-300 hover:bg-yns_orange hover:bg-opacity-20 hover:text-white'">
  <i class="fa-solid fa-{{ $icon }}"></i>
  <span x-show="sidebarOpen">{{ $label }}</span>
</button>
