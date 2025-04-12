/**
 * Shows a modal by ID
 * @param {string} modalId - The ID of the modal to show
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    modal.setAttribute("aria-hidden", "false");
    modal.focus();
}

/**
 * Hides a modal by ID
 * @param {string} modalId - The ID of the modal to hide
 */
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove("flex");
    modal.classList.add("hidden");
    modal.setAttribute("aria-hidden", "true");
}

/**
 * Initializes review modal functionality
 */
export function initReviewModal() {
    // Event listener for buttons to show the modal
    const toggleButtons = document.querySelectorAll("[data-modal-toggle]");
    const closeButtons = document.querySelectorAll("[data-modal-hide]");

    toggleButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const modalId = button.getAttribute("data-modal-toggle");
            if (modalId) showModal(modalId);
        });
    });

    // Event listener for modal close buttons
    closeButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const modalId = button.getAttribute("data-modal-hide");
            if (modalId) hideModal(modalId);
        });
    });

    // Close modal when clicking outside of it
    document.addEventListener("click", (event) => {
        // Check if event target is an HTMLElement before using closest
        if (event.target instanceof HTMLElement) {
            const modal = event.target.closest(".fixed");
            if (modal && event.target === modal) {
                hideModal(modal.id);
            }
        }
    });
}
