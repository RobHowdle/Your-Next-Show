/**
 * Initializes Alpine.js and SweetAlert2, sets up global utility functions, and configures various jQuery functionalities.
 *
 * @file /Users/robhowdle/Files/findmyvenue/resources/js/app.js
 * @requires alpinejs
 * @requires sweetalert2
 * @requires jquery
 *
 * @global
 * @function formatCurrency
 * @param {number} value - The value to format as currency.
 * @returns {string} - The formatted currency string.
 *
 * @global
 * @function formatDateToDMY
 * @param {string} dateString - The date string to format.
 * @returns {string} - The formatted date string in DMY format.
 *
 * @global
 * @function initialiseSummernote
 * @param {string} selector - The jQuery selector for the Summernote editor.
 * @param {string} initialContent - The initial content to set in the Summernote editor.
 *
 * @global
 * @function showSuccessNotification
 * @param {string} message - The success message to display.
 *
 * @global
 * @function showFailureNotification
 * @param {string} message - The failure message to display.
 *
 * @global
 * @function showWarningNotification
 * @param {string} message - The warning message to display.
 *
 * @global
 * @function showConfirmationNotification
 * @param {Object} options - The options for the confirmation notification.
 * @param {string} options.text - The text to display in the confirmation notification.
 * @returns {Promise} - A promise that resolves when the user responds to the confirmation.
 *
 * @global
 * @function showEventBlock
 * @param {Object} info - The event information.
 * @param {Object} info.event - The event object.
 * @param {Object} info.event._def.extendedProps - The extended properties of the event.
 * @param {string} info.event._def.extendedProps.event_start_time - The start time of the event.
 * @param {string} info.event._def.extendedProps.description - The description of the event.
 * @param {Array<string>} info.event._def.extendedProps.bands - The bands performing at the event.
 * @param {string} info.event._def.extendedProps.location - The location of the event.
 * @param {string} info.event._def.extendedProps.ticket_url - The URL for event tickets.
 * @param {string} info.event._def.extendedProps.on_the_door_ticket_price - The on-the-door ticket price.
 * @returns {Promise} - A promise that resolves when the user responds to the event block.
 */
import Alpine from "alpinejs";
import Swal from "../../node_modules/sweetalert2";
import $ from "jquery";
window.$ = window.jQuery = $;

window.Alpine = Alpine;
Alpine.start();

window.Swal = Swal;

// Format currency helper
window.formatCurrency = function (value) {
    return new Intl.NumberFormat("en-GB", {
        style: "currency",
        currency: "GBP",
    }).format(value);
};

// Format Dates
window.formatDateToDMY = function (dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0"); // Pad with zero if needed
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are 0-based
    const year = date.getFullYear();

    return `${day}-${month}-${year}`; // Return in DMY format
};

// Find all disabled elements and add tooltip
// $(document).ready(function () {
//     $(".disabled").each(function () {
//         // Add required classes
//         $(this).addClass("relative group cursor-not-allowed");

//         // Add opacity if not already styled
//         if (!$(this).hasClass("opacity-60")) {
//             $(this).addClass("opacity-60");
//         }

//         // Insert tooltip span if it doesn't exist
//         if (!$(this).find(".tooltip-span").length) {
//             $(this).append(`
//                 <span class="tooltip-span invisible group-hover:visible absolute left-1/2 -translate-x-1/2 -bottom-8
//                     w-max px-2 py-1 bg-black/80 text-white text-sm rounded z-50">
//                     Coming Soon
//                 </span>
//             `);
//         }
//     });
// });

// Accordions
$(document).ready(function () {
    // Accordion functionality
    $("[data-accordion-target]").click(function () {
        const isExpanded = $(this).attr("aria-expanded") === "true";
        const accordionBody = $(this).attr("data-accordion-target");

        $(this).find("svg.icon").toggleClass("rotate-180");

        if (isExpanded) {
            $(this).attr("aria-expanded", "false");
            $(accordionBody).slideUp().addClass("hidden");
        } else {
            $(accordionBody).slideDown().removeClass("hidden");
            $(this).attr("aria-expanded", "true");
        }
    });

    // Hide accordion content by default
    $(".accordion-content").hide();

    $(".accordion-item .accordion-title").click(function () {
        // Toggle active class to show/hide accordion content
        $(this).parent().toggleClass("active");
        $(this).parent().find(".accordion-content").slideToggle();
        $(".accordion-item")
            .not($(this).parent())
            .removeClass("active")
            .find(".accordion-content")
            .slideUp();

        // Prevent checkbox from being checked/unchecked when clicking on label
        var checkbox = jQuery(this).siblings('input[type="checkbox"]');
        checkbox.prop("checked", !checkbox.prop("checked"));
    });

    // Function to close all accordion items
    function closeAllAccordions() {
        jQuery(".accordion-item").removeClass("active");
        jQuery(".accordion-content").slideUp().addClass("hidden");
    }

    // Click outside to close the accordion
    jQuery(document).click(function (event) {
        // Check if the click is outside the accordion
        if (
            !jQuery(event.target).closest(
                ".accordion-item, [data-accordion-target]"
            ).length
        ) {
            closeAllAccordions();
        }
    });

    // Prevent clicks inside the accordion from closing it
    jQuery(".accordion-item").click(function (event) {
        event.stopPropagation();
    });

    jQuery(document).ready(function () {
        // Cache selectors for performance
        var $tabs = jQuery(".tabLinks");
        var $tabContents = jQuery(".venue-tab-content > div");

        // Hide all tab contents except the first one
        $tabContents.not(":first").hide();

        // Add active class to the default tab link
        $tabs
            .first()
            .addClass(
                "active text-yns_yellow border-b-2 border-yns_yellow rounded-t-lg group"
            );

        // Add click event to tab links
        $tabs.click(function (e) {
            e.preventDefault(); // Prevent the default anchor click behavior

            // Get the tab ID from the data attribute
            var tabId = jQuery(this).data("tab");

            // Hide all tab contents and show the selected one
            $tabContents.hide();
            jQuery("#" + tabId).fadeIn();

            // Remove "active" class from all tab links
            $tabs.removeClass(
                "active text-yns_yellow border-b-2 border-yns_yellow rounded-t-lg group"
            );

            // Add "active" class to the clicked tab link
            jQuery(this).addClass(
                "active text-yns_yellow border-b-2 border-yns_yellow rounded-t-lg group"
            );
        });
    });
});

// Review Modal JS
// document.addEventListener("DOMContentLoaded", function () {
//     // Function to show the modal
//     function showModal(modalId) {
//         const modal = document.getElementById(modalId);

//         if (modal) {
//             modal.classList.remove("hidden");
//             modal.classList.add("flex"); // Add 'flex' for display
//             modal.setAttribute("aria-hidden", "false");
//             modal.focus();
//         }
//     }

//     // Function to hide the modal
//     function hideModal(modalId) {
//         const modal = document.getElementById(modalId);

//         if (modal) {
//             modal.classList.remove("flex"); // Remove 'flex'
//             modal.classList.add("hidden");
//             modal.setAttribute("aria-hidden", "true");
//         }
//     }

//     // Event listener for buttons to show the modal
//     document.querySelectorAll("[data-modal-toggle]").forEach((button) => {
//         button.addEventListener("click", function () {
//             const modalId = this.getAttribute("data-modal-toggle");
//             showModal(modalId);
//         });
//     });

//     // Event listener for modal close buttons
//     document.querySelectorAll("[data-modal-hide]").forEach((button) => {
//         button.addEventListener("click", function () {
//             const modalId = this.getAttribute("data-modal-hide");
//             hideModal(modalId);
//         });
//     });

//     // Close modal when clicking outside of it
//     document.addEventListener("click", function (event) {
//         const modal = event.target.closest(".fixed"); // Check for modal clicks
//         if (modal && event.target === modal) {
//             hideModal(modal.id);
//         }
//     });
// });

// Ratings
document.addEventListener("DOMContentLoaded", function () {
    const emptyIcon = "{{ asset('storage/images/system/ratings/empty.png') }}";
    const fullIcon = "{{ asset('storage/images/system/ratings/full.png') }}";
    const hotIcon = "{{ asset('storage/images/system/ratings/hot.png') }}";

    const checkboxes = document.querySelectorAll(
        '.rating input[type="checkbox"]'
    );

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            const value = parseInt(this.value);
            const group = this.name;

            const checkboxesInGroup = document.querySelectorAll(
                `input[name="${group}"]`
            );

            // Check if 5/5 is selected
            if (value === 5) {
                checkboxesInGroup.forEach((cb) => {
                    if (parseInt(cb.value) === 5) {
                        cb.checked = true;
                    }
                    cb.nextElementSibling.style.backgroundImage = `url('${hotIcon}')`;
                });
            } else {
                // Update the checkboxes based on selected value
                checkboxesInGroup.forEach((cb) => {
                    if (parseInt(cb.value) <= value) {
                        cb.checked = true;
                        cb.nextElementSibling.style.backgroundImage = `url('${fullIcon}')`;
                    } else {
                        cb.checked = false;
                        cb.nextElementSibling.style.backgroundImage = `url('${emptyIcon}')`;
                    }
                });
            }
        });
    });
});

// Reviewer IP
jQuery(document).ready(function () {
    $.getJSON("https://api.ipify.org?format=json", function (data) {
        var userIP = data.ip;
        // Verify the element exists before setting the value
        var reviewerIpField = jQuery("#reviewer_ip");
        if (reviewerIpField.length) {
            reviewerIpField.val(userIP);
        }
    }).fail(function (jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.error("Request Failed: " + err);
    });
});

// Function to initialize Summernote
window.initialiseSummernote = function (selector, initialContent) {
    $(selector).summernote({
        height: 300,
        toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "italic", "underline", "clear"]],
            ["fontname", ["fontname"]],
            ["fontsize", ["fontsize"]],
            ["fontSizeUnits", ["px", "pt"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["table", ["table"]],
            ["insert", ["link", "picture", "video"]],
            ["view", ["fullscreen", "help"]],
        ],
        callbacks: {
            onInit: function () {
                jQuery(this).summernote("code", initialContent); // Set the initial content
            },
            onKeyup: function () {
                var editor = jQuery(this);
                var content = editor.summernote("code");

                // Analyze and get the highlighted content
                // var highlightedContent = analyzeText(content);

                // Update only if the content has changed
                // if (highlightedContent !== content) {
                //     // Get the current selection before updating the content
                //     var selection = window.getSelection();
                //     var range = selection.getRangeAt(0);

                //     // Update the content directly
                //     editor.summernote("code", highlightedContent);

                //     // Restore the selection
                //     setTimeout(function () {
                //         // Get the editable area
                //         var $editable = editor.summernote("editable")[0];

                //         // Set the cursor position back to where it was
                //         selection.removeAllRanges(); // Clear existing selections
                //         selection.addRange(range); // Set the new range

                //         // Refocus on the editor
                //         $editable.focus(); // Focus the editable area
                //     }, 0); // Use a small delay to ensure the content is rendered before moving the cursor
                // }
            },
        },
    });
};

// Function to analyze text for venue names

// function analyzeText(inputText) {
//     const venues = [
//         {
//             name: "The Forum",
//             link: "https://www.google.com/theforummusiccenter",
//         },
//         { name: "The Turks Head", link: "https://www.google.com/theturkshead" },
//     ];

//     let highlightedContent = inputText; // Start with the original input text

//     venues.forEach((venue) => {
//         const regex = new RegExp(`\\b(${venue.name})\\b`, "gi");
//         highlightedContent = highlightedContent.replace(
//             regex,
//             `<span class="highlight" data-link="${venue.link}">$1</span>`
//         );
//     });
//     return highlightedContent; // Return the modified content
// }

// Sweet Alert 2 Notifications
window.showSuccessNotification = function (message) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-black",
            html: "text-black",
        },
        icon: "success",
        title: "Success!",
        text: message,
    });
};

window.showFailureNotification = function (message) {
    Swal.fire({
        showConfirmButton: false,
        toast: true,
        position: "top-end",
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-black",
            html: "text-black",
        },
        icon: "error",
        title: "Oops!",
        text: message,
    });
};

window.showWarningNotification = function (message) {
    Swal.fire({
        showConfirmButton: true,
        toast: false,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-yns_red",
            htmlContainer: "!text-white",
            content: "!text-white",
        },
        icon: "warning",
        title: "Warning!",
        text: message,
    });
};

window.showConfirmationNotification = function (options) {
    return Swal.fire({
        showConfirmButton: true,
        confirmButtonText: "I understand",
        showCancelButton: true,
        toast: false,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-white",
            text: "text-white !important",
        },
        icon: "warning",
        title: "Are you sure?",
        text: options.text,
    });
};

// Event Block
window.showEventBlock = function (info) {
    const extendedProps = info.event._def.extendedProps;

    const startTime = extendedProps.event_start_time || "N/A";
    const description = extendedProps.description || "N/A";
    const bands =
        extendedProps.bands && extendedProps.bands.length > 0
            ? extendedProps.bands.join(", ")
            : "N/A";
    const location = extendedProps.location || "N/A";
    const ticketUrl = extendedProps.ticket_url || "N/A";
    const onTheDoorPrice = extendedProps.on_the_door_ticket_price || "N/A";

    return Swal.fire({
        showConfirmButton: true,
        confirmButtonText: "Got it!",
        toast: false,
        icon: "info",
        title: info.event.title,
        html: `
            <strong>Description:</strong> ${description}<br>
            <strong>Start Time:</strong> ${startTime}<br>
            <strong>Bands:</strong> ${bands}<br>
            <strong>Location:</strong> ${location}<br>
            <strong>Ticket URL:</strong> <a href="${ticketUrl}" target="_blank">${
            ticketUrl ? "View Tickets" : "N/A"
        }</a><br>
            <strong>On The Door Price:</strong> £${onTheDoorPrice}<br>
        `,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-white",
            hmtl: "text-white !important",
        },
    });
};

// Full Calendar
document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");

    if (!calendarEl) {
        return; // Exit if the calendar element doesn't exist
    }

    var userId = calendarEl.getAttribute("data-user-id");
    var dashboardType = calendarEl.getAttribute("data-dashboard-type");
    var calendar;

    const calendarTabButton = document.querySelector(
        'button[data-tab="calendar"]'
    );

    calendarTabButton.addEventListener("click", function () {
        if (!calendar) {
            // Only initialize if not already done
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                events: function (fetchInfo, successCallback, failureCallback) {
                    fetch(
                        `/profile/${dashboardType}/events/${userId}?view=calendar&start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`
                    )
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                const eventsArray = data.events.map(
                                    (event) => ({
                                        title: event.title,
                                        start: event.start,
                                        end: event.end,
                                        description: event.description,
                                        event_start_time:
                                            event.event_start_time,
                                        bands: event.bands || [],
                                        location: event.location || "N/A",
                                        ticket_url: event.ticket_url || "N/A",
                                        on_the_door_ticket_price:
                                            event.on_the_door_ticket_price ||
                                            "N/A",
                                    })
                                );
                                successCallback(eventsArray);
                            } else {
                                console.error(
                                    "Error fetching events:",
                                    data.message
                                );
                                failureCallback();
                            }
                        })
                        .catch((error) => {
                            console.error("Error fetching events:", error);
                            failureCallback();
                        });
                },
                eventClick: function (info) {
                    showEventBlock(info);
                },
            });

            calendar.render();
        } else {
            calendar.updateSize();
        }
    });
});
