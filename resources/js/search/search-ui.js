// @ts-nocheck

import $ from "jquery";
import { createEntity, searchEntities } from "services/entity-service";

/**
 * Initializes entity search functionality
 * @param {Object} config - Search configuration
 * @param {string} config.inputSelector - Input element selector
 * @param {string} config.suggestionsSelector - Suggestions list selector
 * @param {string} config.entityType - Type of entity
 * @param {string} config.dashboardType - Type of dashboard
 * @param {Function} config.onSelect - Callback when entity is selected
 * @param {Function} config.onCreate - Callback when entity is created
 */
export function initEntitySearch(config) {
    const {
        inputSelector,
        suggestionsSelector,
        entityType,
        dashboardType,
        onSelect,
        onCreate,
    } = config;

    const input = $(inputSelector);
    const suggestions = $(suggestionsSelector);
    let debounceTimer;

    function handleSearch(query) {
        if (query.length < 3) {
            suggestions.addClass("hidden");
            return;
        }

        searchEntities({ entityType, query, dashboardType }).then(
            (entities) => {
                suggestions.empty().removeClass("hidden");

                if (entities.length === 0) {
                    appendCreateOption(query);
                    return;
                }

                entities.forEach((entity) => appendEntityOption(entity));

                // Add create option if no exact match
                const exactMatch = entities.some(
                    (e) => e.name.toLowerCase() === query.toLowerCase()
                );
                if (!exactMatch) {
                    appendCreateOption(query);
                }
            }
        );
    }

    function appendEntityOption(entity) {
        $("<li>")
            .addClass(
                "suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white"
            )
            .text(entity.name)
            .on("click", () => {
                onSelect(entity);
                suggestions.addClass("hidden");
            })
            .appendTo(suggestions);
    }

    function appendCreateOption(query) {
        $("<li>")
            .addClass(
                "suggestion-item cursor-pointer px-4 py-2 bg-opac_8_black text-yns_yellow font-bold"
            )
            .html(
                `<i class="fas fa-plus mr-2"></i>Create new ${entityType}: "${query}"`
            )
            .on("click", async () => {
                try {
                    const entity = await createEntity({
                        entityType,
                        name: query,
                        dashboardType,
                    });
                    onCreate(entity);
                    suggestions.addClass("hidden");
                } catch (error) {
                    console.error(`Error creating ${entityType}:`, error);
                }
            })
            .appendTo(suggestions);
    }

    // Input handler with debounce
    input.on("input", function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        debounceTimer = setTimeout(() => handleSearch(query), 300);
    });

    // Hide suggestions when clicking outside
    $(document).on("click", function (e) {
        if (
            !$(e.target).closest(`${inputSelector}, ${suggestionsSelector}`)
                .length
        ) {
            suggestions.addClass("hidden");
        }
    });
}
