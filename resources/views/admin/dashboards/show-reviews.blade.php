<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="relative mb-8 shadow-md sm:rounded-lg">
      <div
        class="min-w-screen-xl mx-auto max-w-screen-xl rounded-lg border border-white bg-yns_dark_gray px-8 py-8 text-white">
        <div class="header border-b border-b-white">
          <h1 class="mb-8 font-heading text-4xl font-bold">Reviews</h1>
          <div class="mb-4 flex justify-start space-x-4">
            <button id="all-reviews-btn" class="rounded-lg bg-yns_teal px-4 py-2 font-bold text-black">All
              Reviews</button>
            <button id="pending-reviews-btn" class="rounded-lg bg-yns_yellow px-4 py-2 font-bold text-black">Pending
              Reviews</button>
          </div>
        </div>

        <table class="w-full border border-white text-left font-sans text-xl rtl:text-right" id="promoterReviewsTable">
          <thead class="border-b border-b-white text-xl text-white underline dark:bg-black">
            <tr>
              <th scope="col" class="px-6 py-4">
                Review
              </th>
              <th scope="col" class="px-6 py-4">
                Author
              </th>
              <th scope="col" class="px-8 py-4">
                Actions
              </th>
            </tr>
          </thead>
          <tbody id="reviewsBody">
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
<script>
  const dashboardType = '{{ $dashboardType }}';
  let currentFilter = '{{ $filter }}'; // Make filter globally accessible

  document.addEventListener('DOMContentLoaded', function() {
    // Initial fetch of reviews
    fetchReviews(dashboardType, currentFilter);

    // Filter button listeners
    document.getElementById('pending-reviews-btn').addEventListener('click', () => {
      currentFilter = 'pending';
      fetchReviews(dashboardType, currentFilter);
    });

    document.getElementById('all-reviews-btn').addEventListener('click', () => {
      currentFilter = 'all';
      fetchReviews(dashboardType, currentFilter);
    });

    // Review action handler
    document.getElementById('reviewsBody').addEventListener('click', function(e) {
      const button = e.target.closest('button');
      if (!button) return;

      const reviewId = button.dataset.reviewId;
      const action = button.dataset.action;
      handleReviewAction(button, action, reviewId);
    });
  });

  // Fetch reviews function
  function fetchReviews(dashboardType, filter) {
    fetch(`/dashboard/${dashboardType}/filtered-reviews/${filter}`)
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .then(data => {
        populateReviewsTable(data.reviews);
      })
      .catch(error => {
        console.error('Error fetching reviews:', error);
        showFailureNotification('Failed to fetch reviews');
      });
  }

  // Handle review actions
  function handleReviewAction(button, action, reviewId) {
    const url = `/dashboard/${dashboardType}/reviews/${reviewId}/${action}`;
    const method = action === 'delete' ? 'DELETE' : 'POST';

    fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          _token: document.querySelector('meta[name="csrf-token"]').content
        })
      })
      .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showSuccessNotification(data.message);
          fetchReviews(dashboardType, currentFilter);
        } else {
          throw new Error(data.message || 'Failed to update review');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showFailureNotification(error.message);
      });
  }

  // Populate reviews table
  function populateReviewsTable(reviews) {
    const tbody = document.getElementById('reviewsBody');
    tbody.innerHTML = '';

    if (reviews.length > 0) {
      reviews.forEach(review => {
        const isApproved = review.review_approved === 1 || review.review_approved === true;
        const isDisplayed = review.display === 1 || review.display === true;

        tbody.innerHTML += `
                <tr class="border-gray-700 odd:dark:bg-black even:dark:bg-gray-900">
                    <td class="max-w-md whitespace-normal break-words px-6 py-4 font-sans text-white">
                        ${review.review_message || review.review || 'No review text'}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-white">
                        ${review.review_author || review.author || 'Anonymous'}
                    </td>
                    <td class="flex gap-2 px-6 py-4">
                        <button 
                            class="approve-button rounded-lg bg-white px-4 py-2 font-heading text-black transition hover:${isApproved ? 'bg-yns_red' : 'bg-yns_yellow'}"
                            data-review-id="${review.id}"
                            data-action="${isApproved ? 'unapprove' : 'approve'}"
                            title="${isApproved ? 'Unapprove Review' : 'Approve Review'}"
                        >
                            <span class="fas ${isApproved ? 'fa-times' : 'fa-check'}"></span>
                        </button>

                        <button 
                            class="display-button rounded-lg bg-white px-4 py-2 font-heading text-black transition hover:${isDisplayed ? 'bg-yns_red' : 'bg-yns_teal'}"
                            data-review-id="${review.id}"
                            data-action="${isDisplayed ? 'hide' : 'show'}"
                            title="${isDisplayed ? 'Hide Review' : 'Show Review'}"
                        >
                            <span class="far ${isDisplayed ? 'fa-eye-slash' : 'fa-eye'}"></span>
                        </button>

                        <button 
                            class="delete-button rounded-lg bg-white px-4 py-2 font-heading text-black transition hover:bg-yns_red"
                            data-review-id="${review.id}"
                            data-action="delete"
                            title="Delete Review"
                        >
                            <span class="fas fa-trash-alt"></span>
                        </button>
                    </td>
                </tr>
            `;
      });
    } else {
      tbody.innerHTML = `<tr><td colspan="3" class="text-center text-white py-4">No reviews found</td></tr>`;
    }
  }
</script>
