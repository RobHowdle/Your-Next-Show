import $ from "jquery";
import {
    showSuccessNotification,
    showFailureNotification,
} from "../utils/swal";

/**
 * Searches for events on the selected platform
 * @param {string} platform - Platform identifier
 * @param {string} query - Search query
 * @returns {Promise} Platform events
 */
export function searchPlatformEvents(platform, query) {
    return Promise.resolve(
        $.ajax({
            url: `/api/platforms/${platform}/search`,
            method: "GET",
            data: { query },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            xhrFields: {
                withCredentials: true,
            },
        })
    );
}

/**
 * Imports venue data from platform event
 * @param {Object} event - Platform event object
 * @param {string} dashboardType - Type of dashboard
 * @returns {Promise} Created or found venue
 */
export async function importVenue(event, dashboardType) {
    try {
        const searchResponse = await Promise.resolve(
            $.ajax({
                url: `/dashboard/${dashboardType}/events/venues/search`,
                method: "GET",
                data: { q: event.venue },
            })
        );

        const venues = searchResponse.venues || [];
        const exactMatch = venues.find(
            (v) => v.name.toLowerCase() === event.venue.toLowerCase()
        );

        if (exactMatch) {
            return exactMatch;
        }

        const createResponse = await Promise.resolve(
            $.ajax({
                url: `/dashboard/${dashboardType}/events/venues/create`,
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: { name: event.venue },
            })
        );

        if (createResponse.success && createResponse.venue) {
            showSuccessNotification("Venue created successfully");
            return createResponse.venue;
        }

        throw new Error("Failed to create venue");
    } catch (error) {
        console.error("Error handling venue:", error);
        showFailureNotification("Failed to handle venue");
        throw error;
    }
}
