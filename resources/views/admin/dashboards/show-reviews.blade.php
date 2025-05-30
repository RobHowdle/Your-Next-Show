<x-app-layout :dashboardType="$dashboardType" :modules="$modules">
  <x-slot name="header">
    <x-sub-nav :userId="$userId" />
  </x-slot>

  <div class="mx-auto w-full max-w-screen-2xl py-16">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900/60 backdrop-blur-xl">
        <!-- Header Section -->
        <div class="border-b border-gray-800 px-6 py-8">
          <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <h1 class="font-heading text-3xl font-bold text-white sm:text-4xl">Reviews</h1>
            <div class="flex flex-wrap gap-3">
              <button id="all-reviews-btn"
                class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
                <span class="fas fa-list-ul mr-2"></span>
                All Reviews
              </button>
              <button id="pending-reviews-btn"
                class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
                <span class="fas fa-clock mr-2"></span>
                Pending Reviews
              </button>
            </div>
          </div>
        </div>

        <div class="divide-y divide-gray-800">
          <!-- Reviews Grid -->
          <div class="grid grid-cols-1 gap-6 p-6" id="reviewsGrid">
            <!-- Reviews will be loaded here -->
          </div>

          <!-- Load More Button -->
          <div class="mt-8 flex justify-center p-6">
            <button id="load-more-btn"
              class="inline-flex hidden items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white ring-1 ring-gray-700 transition hover:bg-gray-700 hover:ring-yns_yellow">
              <span class="fas fa-spinner mr-2"></span>
              Load More
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<script>
  // Global variables
  const dashboardType = '{{ $dashboardType }}';
  let currentFilter = '{{ $filter }}';
  let currentPage = 1;
  let isLoading = false;

  document.addEventListener('DOMContentLoaded', function() {
    // Initialize loading spinner
    const spinner = '<span class="fas fa-circle-notch fa-spin"></span>';

    function showLoading(button) {
      button.disabled = true;
      button.dataset.originalContent = button.innerHTML;
      button.innerHTML = `${spinner} Loading...`;
    }

    function hideLoading(button) {
      button.disabled = false;
      button.innerHTML = button.dataset.originalContent;
    }

    document.addEventListener('click', function(e) {
      const actionButton = e.target.closest('.action-btn');
      if (actionButton) {
        const action = actionButton.dataset.action;
        const reviewId = actionButton.dataset.reviewId;
        if (action && reviewId) {
          handleReviewAction(actionButton, action, reviewId);
        }
      }
    });

    // Fetch reviews function
    function fetchReviews(page = 1, filter = currentFilter) {
      const reviewsGrid = document.getElementById('reviewsGrid');

      // Show loading state
      reviewsGrid.classList.add('opacity-50');

      return fetch(`/dashboard/${dashboardType}/reviews/${filter}?page=${page}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(async response => {
          if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.error) {
            throw new Error(data.error);
          }

          if (page === 1) {
            reviewsGrid.innerHTML = '';
          }

          if (data.reviews.length === 0 && page === 1) {
            reviewsGrid.innerHTML = `
                <div class="col-span-full rounded-lg border border-gray-700 bg-gray-800/50 p-12 text-center">
                    <span class="fas fa-star mb-4 text-4xl text-gray-600"></span>
                    <h3 class="mt-2 text-sm font-medium text-white">No ${filter === 'pending' ? 'pending' : ''} reviews found</h3>
                    <p class="mt-1 text-sm text-gray-400">${filter === 'pending' ? 'Pending reviews will appear here.' : 'Reviews will appear here.'}</p>
                </div>
            `;
          } else {
            data.reviews.forEach(review => {
              reviewsGrid.insertAdjacentHTML('beforeend', generateReviewCard(review));
            });
          }

          document.getElementById('load-more-btn').classList.toggle('hidden', !data.hasMorePages);
          return data;
        })
        .catch(error => {
          console.error('Error:', error);
          reviewsGrid.innerHTML = `
            <div class="col-span-full rounded-lg border border-gray-700 bg-gray-800/50 p-12 text-center">
                <span class="fas fa-exclamation-circle mb-4 text-4xl text-red-500"></span>
                <h3 class="mt-2 text-sm font-medium text-white">Error Loading Reviews</h3>
                <p class="mt-1 text-sm text-gray-400">${error.message || 'Please try refreshing the page.'}</p>
            </div>
        `;
          document.getElementById('load-more-btn').classList.add('hidden');
        })
        .finally(() => {
          reviewsGrid.classList.remove('opacity-50');
        });
    }

    // Update the filter button event listeners
    document.querySelectorAll('#all-reviews-btn, #pending-reviews-btn').forEach(button => {
      button.addEventListener('click', function() {
        if (isLoading) return;

        const newFilter = this.id === 'pending-reviews-btn' ? 'pending' : 'all';
        if (currentFilter === newFilter) return;

        currentFilter = newFilter;
        currentPage = 1;

        const loadMoreBtn = document.getElementById('load-more-btn');
        loadMoreBtn.classList.add('hidden');

        fetchReviews(1, newFilter);
      });
    });

    // Generate review card HTML
    function generateReviewCard(review) {
      // Convert the review_approved value to boolean, handling different data formats
      const isApproved = Boolean(parseInt(review.review_approved)) || review.review_approved === true;
      const isDisplayed = Boolean(parseInt(review.display)) || review.display === true;

      return `
        <div class="review-card group relative overflow-hidden rounded-lg border border-gray-700 bg-gray-800/50 p-6 transition duration-200 hover:border-yns_yellow hover:bg-gray-800 hover:shadow-[0_8px_25px_-5px_rgba(255,255,255,0.15)]">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="fas fa-user-circle text-2xl text-gray-400"></span>
                        <span class="font-medium text-white">${review.review_author || review.author || 'Anonymous'}</span>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                        isApproved ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400'
                    }">
                        ${isApproved ? 'Approved' : 'Pending'}
                    </span>
                </div>
                <p class="line-clamp-2 text-gray-300">${review.review_message || review.review || 'No review text'}</p>
                <div class="flex items-center justify-between border-t border-gray-700 pt-4">
                    <div class="flex items-center gap-2">
                        <button 
                            class="view-details rounded-full p-1.5 text-gray-400 transition duration-200 hover:bg-gray-700 hover:text-yns_yellow"
                            data-review='${JSON.stringify({...review, review_approved: isApproved})}'
                            title="View Details">
                            <span class="fas fa-eye"></span>
                        </button>
                        <button 
                            class="action-btn rounded-full p-1.5 text-gray-400 transition duration-200 hover:bg-gray-700 hover:text-yns_yellow"
                            data-review-id="${review.id}"
                            data-action="${isApproved ? 'unapprove' : 'approve'}"
                            title="${isApproved ? 'Unapprove Review' : 'Approve Review'}">
                            <span class="fas ${isApproved ? 'fa-times' : 'fa-check'}"></span>
                        </button>
                        <button 
                            class="action-btn rounded-full p-1.5 text-gray-400 transition duration-200 hover:bg-gray-700 hover:text-red-500"
                            data-review-id="${review.id}"
                            data-action="delete"
                            title="Delete Review">
                            <span class="fas fa-trash-alt"></span>
                        </button>
                    </div>
                    <span class="text-sm text-gray-400">
                        ${new Date(review.created_at).toLocaleDateString('en-GB', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        })}
                    </span>
                </div>
            </div>
        </div>
    `;
    }

    // Show review details modal
    function showReviewDetails(review) {
      // Define ratings based on dashboard type
      let ratings = [];

      switch (dashboardType) {
        case 'artist':
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Music',
              value: parseInt(review.music_rating) || 0
            },
            {
              label: 'Promotion',
              value: parseInt(review.promotion_rating) || 0
            },
            {
              label: 'Gig Quality',
              value: parseInt(review.gig_quality_rating) || 0
            }
          ];
          break;

        case 'designer':
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Flexibility',
              value: parseInt(review.flexibility_rating) || 0
            },
            {
              label: 'Professionalism',
              value: parseInt(review.professional_rating) || 0
            },
            {
              label: 'Design Quality',
              value: parseInt(review.design_quality_rating) || 0
            }
          ];
          break;

        case 'photographer':
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Flexibility',
              value: parseInt(review.flexibility_rating) || 0
            },
            {
              label: 'Professionalism',
              value: parseInt(review.professional_rating) || 0
            },
            {
              label: 'Photo Quality',
              value: parseInt(review.photo_quality_rating) || 0
            }
          ];
          break;

        case 'videographer':
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Flexibility',
              value: parseInt(review.flexibility_rating) || 0
            },
            {
              label: 'Professionalism',
              value: parseInt(review.professional_rating) || 0
            },
            {
              label: 'Video Quality',
              value: parseInt(review.video_quality_rating) || 0
            }
          ];
          break;

        case 'venue':
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Rate Of Pay',
              value: parseInt(review.rop_rating) || 0
            },
            {
              label: 'Promotion',
              value: parseInt(review.promotion_rating) || 0
            },
            {
              label: 'Quality',
              value: parseInt(review.quality_rating) || 0
            }
          ];
          break;

        case 'promoter':
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Rate Of Pay',
              value: parseInt(review.rop_rating) || 0
            },
            {
              label: 'Promotion',
              value: parseInt(review.promotino_rating) || 0
            },
            {
              label: 'Quality',
              value: parseInt(review.quality_rating) || 0
            }
          ];
          break;

        default:
          ratings = [{
              label: 'Communication',
              value: parseInt(review.communication_rating) || 0
            },
            {
              label: 'Overall Experience',
              value: parseInt(review.overall_rating) || 0
            }
          ];
      }

      const overallRating = (ratings.reduce((sum, r) => sum + r.value, 0) / ratings.length).toFixed(1);

      const ratingStars = (rating) => {
        return `<div class="flex items-center gap-1">
            ${Array(5).fill(0).map((_, i) => 
                `<span class="fas fa-star text-lg ${i < rating ? 'text-yns_yellow' : 'text-gray-600'}"></span>`
            ).join('')}
            <span class="ml-2 text-sm text-gray-400">${rating}/5</span>
        </div>`;
      };

      const ratingsHtml = ratings.map(r => `
            <div class="flex items-center justify-between py-3">
                <span class="text-sm font-medium text-gray-300">${r.label}</span>
                ${ratingStars(r.value)}
            </div>
        `).join('');

      Swal.fire({
        html: `
                <div class="space-y-6 text-left">
                    <div class="flex items-center justify-between border-b border-gray-700 pb-4">
                        <div class="flex items-center space-x-3">
                            <span class="fas fa-user-circle text-2xl text-gray-400"></span>
                            <div>
                                <h3 class="font-medium text-white">
                                    ${review.review_author || review.author || 'Anonymous'}
                                </h3>
                                <p class="text-sm text-gray-400">
                                    ${new Date(review.created_at).toLocaleDateString('en-GB', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    })}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="flex items-center">
                                <span class="mr-2 text-2xl font-bold text-white">${overallRating}</span>
                                <span class="fas fa-star text-lg text-yns_yellow"></span>
                            </div>
                            <span class="text-sm text-gray-400">Overall Rating</span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
                        <h4 class="mb-2 font-medium text-white">Review</h4>
                        <p class="text-gray-300">${review.review_message || review.review || 'No review text'}</p>
                    </div>

                    <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
                        <h4 class="mb-4 font-medium text-white">Detailed Ratings</h4>
                        <div class="divide-y divide-gray-700">
                            ${ratingsHtml}
                        </div>
                    </div>
                </div>
            `,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
          popup: 'bg-gray-900 text-white border border-gray-800 rounded-xl',
          closeButton: 'focus:outline-none focus:ring-2 focus:ring-gray-600 hover:text-white',
          htmlContainer: 'p-0'
        },
        width: 'max-w-xl',
        padding: '1.5rem'
      });
    }

    // Handle review actions (approve, unapprove, delete)
    async function handleReviewAction(button, action, reviewId) {
      if (isLoading) return;

      let url, method, confirmMessage, successMessage;

      // Set up action configuration
      switch (action) {
        case 'approve':
          url = `/dashboard/${dashboardType}/reviews/${reviewId}/approve`;
          method = 'POST';
          confirmMessage = 'Are you sure you want to approve this review?';
          successMessage = 'Review approved successfully';
          break;
        case 'unapprove':
          url = `/dashboard/${dashboardType}/reviews/${reviewId}/unapprove`;
          method = 'POST';
          confirmMessage = 'Are you sure you want to unapprove this review?';
          successMessage = 'Review unapproved successfully';
          break;
        case 'delete':
          url = `/dashboard/${dashboardType}/reviews/${reviewId}`; // Updated URL
          method = 'DELETE';
          confirmMessage = 'Are you sure you want to delete this review?';
          successMessage = 'Review deleted successfully';
          break;
        default:
          return;
      }

      // Get user confirmation
      const result = await Swal.fire({
        title: 'Confirm Action',
        text: confirmMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FFB800',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, proceed'
      });

      if (!result.isConfirmed) return;

      try {
        showLoading(button);
        isLoading = true;

        const response = await fetch(url, {
          method: method,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const data = await response.json();

        // Find review card and verify it exists
        const reviewCard = button.closest('.review-card');
        if (!reviewCard) {
          throw new Error('Review card not found');
        }

        // Handle pending view approve action
        if (currentFilter === 'pending' && action === 'approve') {
          reviewCard.remove();

          // Check if we need to show empty state
          const reviewsGrid = document.getElementById('reviewsGrid');
          if (reviewsGrid && reviewsGrid.children.length === 0) {
            reviewsGrid.innerHTML = `
                    <div class="col-span-full rounded-lg border border-gray-700 bg-gray-800/50 p-12 text-center">
                        <span class="fas fa-star mb-4 text-4xl text-gray-600"></span>
                        <h3 class="mt-2 text-sm font-medium text-white">No pending reviews found</h3>
                        <p class="mt-1 text-sm text-gray-400">Pending reviews will appear here.</p>
                    </div>
                `;
          }

        } else {
          // Update UI elements if they exist
          try {
            // Find elements within the review card
            const statusBadge = reviewCard.querySelector('.inline-flex.items-center.rounded-full');
            const actionIcon = button.querySelector('.fas');

            if (action === 'approve') {
              if (statusBadge) {
                statusBadge.className =
                  'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-500/10 text-green-400';
                statusBadge.textContent = 'Approved';
              }
              if (button) {
                button.dataset.action = 'unapprove';
                button.title = 'Unapprove Review';
              }
              if (actionIcon) {
                actionIcon.className = 'fas fa-times';
              }
            } else if (action === 'unapprove') {
              if (statusBadge) {
                statusBadge.className =
                  'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-500/10 text-yellow-400';
                statusBadge.textContent = 'Pending';
              }
              if (button) {
                button.dataset.action = 'approve';
                button.title = 'Approve Review';
              }
              if (actionIcon) {
                actionIcon.className = 'fas fa-check';
              }
            } else if (action === 'delete') {
              reviewCard.remove();
            }
          } catch (uiError) {
            console.error('Error updating UI:', uiError);
            // Fallback to refreshing the reviews
            await fetchReviews(currentPage, currentFilter);
          }
        }

        // Show success message
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: successMessage,
          timer: 1500,
          showConfirmButton: false
        });

      } catch (error) {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to process your request. Please try again.',
          confirmButtonColor: '#FFB800'
        });
      } finally {
        isLoading = false;
        hideLoading(button);
      }
    }

    // Initialize reviews on page load
    fetchReviews();

    // Event Listeners
    document.querySelectorAll('#all-reviews-btn, #pending-reviews-btn').forEach(button => {
      button.addEventListener('click', function() {
        const newFilter = this.id === 'pending-reviews-btn' ? 'pending' : 'all';
        if (currentFilter === newFilter) return;

        currentFilter = newFilter;
        currentPage = 1;
        fetchReviews(1, currentFilter);
      });
    });

    // Load more button
    document.getElementById('load-more-btn').addEventListener('click', function() {
      if (isLoading) return;

      const button = this;
      showLoading(button);
      isLoading = true;

      fetchReviews(++currentPage)
        .finally(() => {
          isLoading = false;
          hideLoading(button);
        });
    });

    // Review grid actions
    document.getElementById('reviewsGrid').addEventListener('click', function(e) {
      const button = e.target.closest('button');
      if (!button) return;

      if (button.classList.contains('view-details')) {
        const review = JSON.parse(button.dataset.review);
        showReviewDetails(review);
      } else if (button.classList.contains('action-btn')) {
        const reviewId = button.dataset.reviewId;
        const action = button.dataset.action;
        handleReviewAction(button, action, reviewId);
      }
    });
  });
</script>
