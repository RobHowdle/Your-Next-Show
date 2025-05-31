import Swal from "sweetalert2";

const DEFAULT_TIMER = 3000;

/**
 * Shows a success notification toast
 * @param {string} message - The message to display
 * @param {number} [timer=DEFAULT_TIMER] - Duration in milliseconds to show the toast
 */
export function showSuccessNotification(message, timer = DEFAULT_TIMER) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: timer,
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
export function showFailureNotification(message, timer = DEFAULT_TIMER) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: timer,
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
export function showWarningNotification(message, timer = DEFAULT_TIMER) {
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
 * Shows a confirmation notification
 * @param {string|Object} messageOrOptions - Either a message string or options object
 * @param {number} [timer=DEFAULT_TIMER] - Duration in milliseconds (used when first param is a string)
 * @returns {Promise} SweetAlert2 promise
 */
export function showConfirmationNotification(
    messageOrOptions,
    timer = DEFAULT_TIMER
) {
    // Check if first parameter is a string (simple usage) or an object (advanced usage)
    if (typeof messageOrOptions === "string") {
        // Simple usage: showConfirmationNotification("Your message", 3000)
        return Swal.fire({
            showConfirmButton: false,
            toast: true,
            position: "top-end",
            timer: timer,
            timerProgressBar: true,
            customClass: {
                popup: "bg-yns_dark_gray !important rounded-lg font-heading",
                title: "text-black",
                htmlContainer: "text-black",
            },
            icon: "info",
            title: "Information",
            text: messageOrOptions,
        });
    } else {
        // Advanced usage: showConfirmationNotification({text: "Are you sure?", onConfirm: () => {}})
        const options = messageOrOptions;
        return Swal.fire({
            showConfirmButton: true,
            confirmButtonText: "I understand",
            showCancelButton: true,
            toast: false,
            customClass: {
                popup: "bg-yns_dark_gray !important rounded-lg font-heading",
                title: "text-white",
                htmlContainer: "!text-white",
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
}

/**
 * Shows a scheduled notification toast
 * @param {string} message - The message to display
 * @param {string|null} scheduledTime - Optional scheduled time to display
 */
export function showScheduledNotification(
    message,
    scheduledTime = null,
    timer = DEFAULT_TIMER
) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: 5000,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-black",
            container: "text-black",
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

/**
 * Shows a test notification toast
 * @param {string} message - The message to display
 */
export function showTestNotification(message, timer = DEFAULT_TIMER) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: timer,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-black",
            htmlContainer: "text-black",
        },
        icon: "success",
        text: message,
    });
}
