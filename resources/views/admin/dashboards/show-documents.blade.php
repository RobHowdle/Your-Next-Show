<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray px-8 py-8 text-white">
        <div class="header mb-8">
          <div class="flex flex-row justify-between">
            <h1 class="font-heading text-4xl font-bold">Documents</h1>
            <x-button href="{{ route('admin.dashboard.document.create', ['dashboardType' => $dashboardType]) }}"
              label="New Document"></x-button>
          </div>
        </div>
        <table class="w-full border border-white text-left font-sans" id="documents">
          <thead class="border border-b-white bg-black text-white underline">
            <tr>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl"
                onclick="sortTable('title')">Document</th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl"
                onclick="sortTable('description')">Description</th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl"
                onclick="sortTable('category')">Category</th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl"
                onclick="sortTable('created_at')">Uploaded</th>
              <th scope="col" class="px-2 py-2 text-base md:px-6 md:py-3 md:text-xl lg:px-8 lg:py-4 lg:text-2xl">
                Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($documents as $document)
              <tr id="document-row-{{ $document->id }}" class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
                <th scope="row"
                  class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                  <a class="transition-all duration-150 ease-in-out hover:text-yns_yellow"
                    href="{{ route('admin.dashboard.document.show', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}">{{ $document->title }}</a>
                </th>
                <td
                  class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                  {{ $document->description }}
                </td>
                <td
                  class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                  @php
                    $categories = json_decode($document->category, true) ?? [];
                  @endphp

                  @if (!empty($categories) && is_array($categories))
                    @foreach ($categories as $tag)
                      <span class="inline-block rounded-full bg-yns_yellow px-2 py-1 text-xs font-semibold text-black">
                        {{ htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') }}
                      </span>
                    @endforeach
                  @else
                    No categories available
                  @endif
                </td>

                <td
                  class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                  {{ $document->created_at->format('d-m-Y') }}
                </td>
                <td
                  class="whitespace-nowrap px-2 py-2 font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                  <a href="{{ route('admin.dashboard.document.download', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                    class="hover:text-yns_yellow"><span class="fas fa-download"></span></a>
                  <a href="{{ route('admin.dashboard.document.edit', ['dashboardType' => $dashboardType, 'id' => $document->id]) }}"
                    class="ml-4 hover:text-yns_yellow"><span class="fas fa-edit"></span></a>
                  <button class="delete-btn ml-4 hover:text-yns_red" data-id="{{ $document->id }}"
                    data-dashboard-type="{{ $dashboardType }}"><span class="fas fa-trash-alt"></span>
                  </button>

                </td>
              </tr>
            @empty
              <tr class="border-b border-white odd:dark:bg-black even:dark:bg-gray-900">
                <td colspan="5"
                  class="whitespace-nowrap px-2 py-2 text-center font-sans text-white md:px-6 md:py-3 md:text-base lg:px-8 lg:py-4 lg:text-lg">
                  No documents found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
</x-app-layout>
<script>
  function sortTable(column) {
    const table = document.getElementById('documents');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    const isAscending = tbody.dataset.sort === column ? false : true;
    tbody.dataset.sort = column;

    rows.sort((a, b) => {
      const aText = a.querySelector(`td:nth-child(${getColumnIndex(column)})`).innerText;
      const bText = b.querySelector(`td:nth-child(${getColumnIndex(column)})`).innerText;

      if (column === 'created_at') {
        return isAscending ?
          new Date(aText.split('-').reverse().join('-')) - new Date(bText.split('-').reverse().join('-')) :
          new Date(bText.split('-').reverse().join('-')) - new Date(aText.split('-').reverse().join('-'));
      } else {
        return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
      }
    });

    rows.forEach(row => tbody.appendChild(row));
  }

  function getColumnIndex(column) {
    switch (column) {
      case 'title':
        return 1; // Document Title
      case 'description':
        return 2; // Description
      case 'category':
        return 3; // Category
      case 'created_at':
        return 4; // Upload Date
      default:
        return -1; // Invalid index
    }
  }

  jQuery(document).on('click', '.delete-btn', function() {
    var documentId = jQuery(this).data('id');
    var dashboardType = jQuery(this).data('dashboard-type');

    showConfirmationNotification({
      text: 'Are you sure you want to delete this document?'
    }).then((result) => {
      if (result) {
        $.ajax({
          url: `/dashboard/${dashboardType}/documents/${documentId}`,
          type: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              showSuccessNotification(response.message);
              jQuery(`#document-row-${documentId}`).remove();
            } else {
              swal("Error!", response.error, "error");
            }
          },
          error: function(xhr) {
            showFailureNotification(response.message);
          }
        });
      }
    });
  });
</script>
