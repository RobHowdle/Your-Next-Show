<header>
  <h2 class="text-md font-heading font-medium text-white">
    {{ __('Your Recent Events') }}
  </h2>
</header>
<div class="group mb-6">
  <table class="mt-4 w-full border border-white text-left font-sans text-xl rtl:text-right">
    <thead class="border-b border-b-white text-xl text-white underline dark:bg-black">
      <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Event Name</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Date</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Gig Rating</th>
        <th class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">Actions</th>
      </tr>
    </thead>
    <tbody>
      @if (!isset($profileData['myEvents']) || (isset($profileData['myEvents']) && $profileData['myEvents']->isEmpty()))
        <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
          <td class="max-w-md whitespace-normal break-words px-6 py-4 text-center font-sans text-white" colspan="4">
            No events
            found.</td>
        </tr>
      @else
        @foreach ($profileData['myEvents'] as $event)
          <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">{{ $event->event_name }}
            </td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
              {{ $event->event_date }}
            </td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">{{ $event->rating }}</td>
            <td class="max-w-md whitespace-normal break-words px-6 py-4 text-center font-sans text-white">
              <a href="{{ route('admin.dashboard.show-event', ['dashboardType' => $dashboardType, 'id' => $event->id]) }}"
                class="text-blue-500 hover:underline">View</a>
              <a href="{{ route('admin.dashboard.edit-event', ['dashboardType' => $dashboardType, 'id' => $event->id]) }}"
                class="ml-2 text-yellow-500 hover:underline">Edit</a>
              <a href="{{ route('admin.dashboard.delete-event', ['dashboardType' => $dashboardType, 'id' => $event->id]) }}"
                class="ml-2 text-yellow-500 hover:underline">Delete</a>
            </td>
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>
</div>
