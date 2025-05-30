import $ from "jquery";
import {
    showSuccessNotification,
    showFailureNotification,
} from "../utils/swal";

/**
 * Creates a new entity via API
 * @param {Object} params - Creation parameters
 * @param {string} params.entityType - Type of entity (band/venue/promoter)
 * @param {string} params.name - Name of the entity
 * @param {string} params.dashboardType - Type of dashboard
 * @returns {Promise} Created entity
 */
export async function createEntity({ entityType, name, dashboardType }) {
    try {
        const response = await $.ajax({
            url: `/dashboard/${dashboardType}/events/${entityType}s/create`,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { name },
        });

        if (response.success) {
            showSuccessNotification(`${entityType} created successfully`);
            return response[entityType];
        }
        throw new Error(response.message || `Failed to create ${entityType}`);
    } catch (error) {
        showFailureNotification(`Failed to create ${entityType}`);
        throw error;
    }
}

/**
 * Searches for entities
 * @param {Object} params - Search parameters
 * @param {string} params.entityType - Type of entity to search
 * @param {string} params.query - Search query
 * @param {string} params.dashboardType - Type of dashboard
 * @returns {Promise} Search results
 */
export async function searchEntities({ entityType, query, dashboardType }) {
    try {
        const response = await $.ajax({
            url: `/dashboard/${dashboardType}/events/${entityType}s/search`,
            method: "GET",
            data: { q: query },
        });
        return response[`${entityType}s`] || [];
    } catch (error) {
        console.error(`Error searching ${entityType}s:`, error);
        return [];
    }
}
