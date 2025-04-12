// @ts-nocheck

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import Swal from "sweetalert2";

/**
 * Fetches events for the calendar
 * @param {Object} fetchInfo - Calendar fetch info object
 * @param {string} dashboardType - Type of dashboard
 * @param {string} userId - User ID
 * @returns {Promise} Promise that resolves with events array
 */
function fetchCalendarEvents(fetchInfo, dashboardType, userId) {
    return fetch(
        `/profile/${dashboardType}/events/${userId}?view=calendar&start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`
    )
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                throw new Error(data.message || "Failed to fetch events");
            }

            return data.events.map((event) => ({
                title: event.title,
                start: event.start,
                end: event.end,
                description: event.description,
                event_start_time: event.event_start_time,
                bands: event.bands || [],
                location: event.location || "N/A",
                ticket_url: event.ticket_url || "N/A",
                on_the_door_ticket_price:
                    event.on_the_door_ticket_price || "N/A",
            }));
        });
}

/**
 * Initializes the calendar
 * @param {HTMLElement} element - Calendar container element
 * @param {string} dashboardType - Type of dashboard
 * @param {string} userId - User ID
 * @returns {Calendar} Calendar instance
 */
function initializeCalendar(element, dashboardType, userId) {
    return new Calendar(element, {
        plugins: [dayGridPlugin],
        initialView: "dayGridMonth",
        events: (fetchInfo, successCallback, failureCallback) => {
            fetchCalendarEvents(fetchInfo, dashboardType, userId)
                .then(successCallback)
                .catch((error) => {
                    console.error("Error fetching events:", error);
                    failureCallback();
                });
        },
        eventClick: (info) => showEventBlock(info),
    });
}

/**
 * Shows event details in a modal
 * @param {Object} info - Event information
 * @returns {Promise} SweetAlert2 promise
 */
function showEventBlock(info) {
    const props = info.event._def.extendedProps;

    return Swal.fire({
        showConfirmButton: true,
        confirmButtonText: "Got it!",
        toast: false,
        icon: "info",
        title: info.event.title,
        html: `
            <strong>Description:</strong> ${props.description || "N/A"}<br>
            <strong>Start Time:</strong> ${props.event_start_time || "N/A"}<br>
            <strong>Bands:</strong> ${
                props.bands?.length ? props.bands.join(", ") : "N/A"
            }<br>
            <strong>Location:</strong> ${props.location || "N/A"}<br>
            <strong>Ticket URL:</strong> <a href="${
                props.ticket_url
            }" target="_blank">${
            props.ticket_url ? "View Tickets" : "N/A"
        }</a><br>
            <strong>On The Door Price:</strong> £${
                props.on_the_door_ticket_price || "N/A"
            }<br>
        `,
        customClass: {
            popup: "bg-yns_dark_gray !important rounded-lg font-heading",
            title: "text-white",
            htmlContainer: "!text-white",
        },
    });
}

/**
 * Initializes calendar functionality
 */
export function initCalendar() {
    const calendarEl = document.getElementById("calendar");
    if (!calendarEl) return;

    const userId = calendarEl.getAttribute("data-user-id");
    const dashboardType = calendarEl.getAttribute("data-dashboard-type");
    let calendar;

    const calendarTabButton = document.querySelector(
        'button[data-tab="calendar"]'
    );
    if (!calendarTabButton) return;

    calendarTabButton.addEventListener("click", () => {
        if (!calendar) {
            calendar = initializeCalendar(calendarEl, dashboardType, userId);
            calendar.render();
        } else {
            calendar.updateSize();
        }
    });
}

// Make showEventBlock available globally if needed
window.showEventBlock = showEventBlock;
