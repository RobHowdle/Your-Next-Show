import Swal from "sweetalert2";

/**
 * Shows a success notification toast
 * @param {string} message - The message to display
 */
export function showSuccessNotification(message) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-black",
            htmlContainer: "text-black",
        },
        icon: "success",
        title: "Success!",
        text: message,
    });
}

/**
 * Shows an error notification toast
 * @param {string} message - The error message to display
 */
export function showFailureNotification(message) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-black",
            htmlContainer: "text-black",
        },
        icon: "error",
        title: "Oops!",
        text: message,
    });
}

/**
 * Shows a warning notification modal
 * @param {string} message - The warning message to display
 */
export function showWarningNotification(message) {
    Swal.fire({
        showConfirmButton: true,
        toast: false,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-yns_red",
            htmlContainer: "!text-white",
        },
        icon: "warning",
        title: "Warning!",
        text: message,
    });
}

/**
 * Shows a confirmation dialog
 * @param {Object} options - Configuration options
 * @param {string} options.text - The confirmation message
 * @param {Function} options.onConfirm - Callback function when confirmed
 * @returns {Promise} SweetAlert2 promise
 */
export function showConfirmationNotification(options) {
    return Swal.fire({
        showConfirmButton: true,
        confirmButtonText: "I understand",
        showCancelButton: true,
        toast: false,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-white",
            htmlContainer: "!text-white", // Changed from text to htmlContainer
            confirmButton: "bg-yns_yellow text-black",
            cancelButton: "bg-gray-600 text-white",
        },
        icon: "warning",
        title: "Are you sure?",
        text: options.text,
    }).then((result) => {
        if (result.isConfirmed && typeof options.onConfirm === "function") {
            options.onConfirm();
        }
    });
}

/**
 * Shows a scheduled notification toast
 * @param {string} message - The message to display
 * @param {string|null} scheduledTime - Optional scheduled time to display
 */
export function showScheduledNotification(message, scheduledTime = null) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: 5000,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-white",
            container: "!text-white",
            icon: "text-yns_yellow",
        },
        icon: "info",
        title: "Scheduled!",
        html: `
            <div class="flex items-center space-x-2">
                <i class="fas fa-clock text-yns_yellow"></i>
                <div>
                    <p class="text-white">${message}</p>
                    ${
                        scheduledTime
                            ? `<p class="text-sm text-gray-400 mt-1">Scheduled for: ${scheduledTime}</p>`
                            : ""
                    }
                </div>
            </div>
        `,
    });
}
