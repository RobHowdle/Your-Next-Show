// @ts-nocheck

import $ from "jquery";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import {
    showSuccessNotification,
    showFailureNotification,
} from "../utils/swal";
import { createEntity, searchEntities } from "../services/entity-service";
import { searchPlatformEvents, importVenue } from "../services/platform-events";
import {
    initOpportunityModal,
    handleCreateListing,
} from "../components/opportunity-modal";

let dashboardType;

export function initEventForm(config) {
    dashboardType = config.dashboardType;

    $(document).ready(function () {
        initializeDatePickers();
        initializeAllEntitySearches();
        initializeFormSubmission();
        initializePosterUpload();
        initializePlatformEvents();

        // Initialize opportunity modal only once
        if ($("#opportunityModal").length) {
            initOpportunityModal({ dashboardType });
        }

        // Add click handler for the create opportunity button
        $("#createOpportunityBtn").on("click", function () {
            const eventData = {
                headlinerId: $("#headliner_id").val(),
                mainSupportId: $("#main_support_id").val(),
                openerId: $("#opener_id").val(),
                bandsIds: $("#bands_ids").val(),
            };

            const modal = $("#opportunityModal");
            modal.removeClass("hidden").css("display", "block");
            modal.trigger("prefill-opportunity", [eventData]);
        });
    });
}

function initializeDatePickers() {
    flatpickr("#event_date", {
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "d-m-Y",
    });

    flatpickr("#event_start_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    });

    flatpickr("#event_end_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    });
}

function initializeAllEntitySearches() {
    const entitySearchConfigs = [
        {
            inputSelector: "#headliner-search",
            suggestionsSelector: "#headliner-suggestions",
            entityType: "band",
            idField: "#headliner_id",
        },
        {
            inputSelector: "#main-support-search",
            suggestionsSelector: "#main-support-suggestions",
            entityType: "band",
            idField: "#main_support_id",
        },
        {
            inputSelector: "#bands-search",
            suggestionsSelector: "#bands-suggestions",
            entityType: "band",
            idField: "#bands_ids",
            isMultiple: true,
        },
        {
            inputSelector: "#opener-search",
            suggestionsSelector: "#opener-suggestions",
            entityType: "band",
            idField: "#opener_id",
        },
        {
            inputSelector: "#venue_name",
            suggestionsSelector: "#venue-suggestions",
            entityType: "venue",
            idField: "#venue_id",
        },
        {
            inputSelector: "#promoter_name",
            suggestionsSelector: "#promoter-suggestions",
            entityType: "promoter",
            idField: "#promoter_ids",
        },
    ];

    entitySearchConfigs.forEach((config) => {
        if (config.isMultiple) {
            initializeMultipleBandSearch(config);
        } else {
            initializeEntitySearch({
                ...config,
                onSelect: (entity) => {
                    $(config.inputSelector).val(entity.name);
                    $(config.idField).val(entity.id);
                },
                onCreate: (entity) => {
                    $(config.inputSelector).val(entity.name);
                    $(config.idField).val(entity.id);
                },
            });
        }
    });
}

function initializeMultipleBandSearch({
    inputSelector,
    suggestionsSelector,
    entityType,
    idField,
}) {
    const input = $(inputSelector);
    const suggestions = $(suggestionsSelector);
    const idsField = $(idField);
    let selectedIds = [];
    let debounceTimer;

    // Handle backspace for removing items
    input.on("keydown", function (e) {
        if (e.key === "Backspace" && !this.value.split(",").pop().trim()) {
            e.preventDefault();
            const values = input
                .val()
                .split(",")
                .map((v) => v.trim())
                .filter((v) => v);
            if (values.length) {
                values.pop();
                selectedIds.pop();
                input.val(values.length ? values.join(", ") + ", " : "");
                idsField.val(selectedIds.join(","));
            }
        }
    });

    // Handle input with debounce
    input.on("input", function () {
        clearTimeout(debounceTimer);
        const query = this.value.split(",").pop().trim();

        if (query.length >= 3) {
            debounceTimer = setTimeout(() => {
                searchEntities({ entityType, query, dashboardType }).then(
                    (entities) => {
                        suggestions.empty().removeClass("hidden");

                        if (entities.length === 0) {
                            appendCreateOption(query);
                            return;
                        }

                        entities.forEach((entity) =>
                            appendEntityOption(entity)
                        );

                        if (
                            !entities.some(
                                (e) =>
                                    e.name.toLowerCase() === query.toLowerCase()
                            )
                        ) {
                            appendCreateOption(query);
                        }
                    }
                );
            }, 300);
        } else {
            suggestions.addClass("hidden");
        }
    });

    function appendEntityOption(entity) {
        $("<li>")
            .addClass(
                "suggestion-item cursor-pointer hover:text-yns_yellow px-4 py-2 bg-opac_8_black text-white"
            )
            .text(entity.name)
            .on("click", () => {
                addBand(entity);
                suggestions.addClass("hidden");
            })
            .appendTo(suggestions);
    }

    function appendCreateOption(query) {
        $("<li>")
            .addClass(
                "suggestion-item cursor-pointer px-4 py-2 bg-opac_8_black text-yns_yellow font-bold"
            )
            .html(`<i class="fas fa-plus mr-2"></i>Create new band: "${query}"`)
            .on("click", async () => {
                try {
                    const entity = await createEntity({
                        entityType,
                        name: query,
                        dashboardType,
                    });
                    addBand(entity);
                    suggestions.addClass("hidden");
                } catch (error) {
                    console.error(`Error creating band:`, error);
                }
            })
            .appendTo(suggestions);
    }

    function addBand(band) {
        const currentValue = input.val();
        const existingBands = currentValue
            .split(",")
            .map((b) => b.trim())
            .filter((b) => b.length > 0)
            .slice(0, -1);

        // Add new band
        existingBands.push(band.name);
        input.val(existingBands.join(", ") + ", ");

        // Update IDs
        selectedIds.push(band.id);
        idsField.val(selectedIds.join(","));
    }

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

function initializeEntitySearch({
    inputSelector,
    suggestionsSelector,
    entityType,
    onSelect,
    onCreate,
}) {
    const input = $(inputSelector);
    const suggestions = $(suggestionsSelector);
    let debounceTimer;

    input.on("input", function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length >= 3) {
            debounceTimer = setTimeout(() => {
                searchEntities({ entityType, query, dashboardType }).then(
                    (entities) => {
                        suggestions.empty().removeClass("hidden");

                        if (entities.length === 0) {
                            appendCreateOption(query);
                            return;
                        }

                        entities.forEach((entity) =>
                            appendEntityOption(entity)
                        );

                        if (
                            !entities.some(
                                (e) =>
                                    e.name.toLowerCase() === query.toLowerCase()
                            )
                        ) {
                            appendCreateOption(query);
                        }
                    }
                );
            }, 300);
        } else {
            suggestions.addClass("hidden");
        }
    });

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

function initializeFormSubmission() {
    $("#eventForm").on("submit", function (event) {
        event.preventDefault();
        const formData = new FormData(this);

        // Handle pending opportunities
        const opportunities = JSON.parse(
            $("#pending_opportunities").val() || "[]"
        );
        formData.delete("pending_opportunities");
        formData.append("pending_opportunities", JSON.stringify(opportunities));

        // Handle IDs arrays
        const bandIds = $("#bands_ids")
            .val()
            .split(",")
            .filter((id) => id.trim());
        const promoterIds = $("#promoter_ids")
            .val()
            .split(",")
            .filter((id) => id.trim());

        formData.delete("bands_ids");
        bandIds.forEach((id) => formData.append("bands_ids[]", id));
        promoterIds.forEach((id) => formData.append("promoter_ids[]", id));

        // Add platform data if present
        if ($("#ticket_platform").val()) {
            formData.append("ticket_platform", $("#ticket_platform").val());
            formData.append("platform_event_id", $("#platform_event_id").val());
            formData.append(
                "platform_event_url",
                $("#platform_event_url").val()
            );
        }

        $.ajax({
            url: `/dashboard/${dashboardType}/events/store-event`,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                if (data.success) {
                    showSuccessNotification(data.message);
                    setTimeout(
                        () => (window.location.href = data.redirect_url),
                        2000
                    );
                } else {
                    handleFormErrors(data.errors);
                }
            },
            error: function (jqXHR) {
                const message =
                    jqXHR.status === 413
                        ? "The uploaded file is too large. Maximum size is 10MB."
                        : "An error occurred. Please try again.";
                showFailureNotification(message);
            },
        });
    });
}

function handleFormErrors(errors) {
    if (errors) {
        Object.keys(errors).forEach((key) =>
            showFailureNotification(errors[key])
        );
    } else {
        showFailureNotification(
            "An unexpected error occurred. Please try again."
        );
    }
}

// Initialize file upload preview
function initializePosterUpload() {
    $("#poster_url").on("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#posterPreview")
                    .attr("src", e.target.result)
                    .removeClass("hidden");
            };
            reader.readAsDataURL(file);
        }
    });
}

function initializePlatformEvents() {
    const ticketPlatform = $("#ticket_platform");
    const modal = $("#platformEventModal");
    const searchInput = $("#platformEventSearch");
    const resultsContainer = $("#platformEventResults");
    const platformEventId = $("#platform_event_id");
    const platformEventUrl = $("#platform_event_url");
    let searchTimeout;

    // Handle import button click
    $("#importEventButton").on("click", function () {
        modal.removeClass("hidden");
        searchInput.val("");
        resultsContainer.empty();
        searchPlatformEvents(ticketPlatform.val(), "").then(
            handleSearchResults
        );
    });

    // Handle search input with debounce
    searchInput.on("input", function () {
        clearTimeout(searchTimeout);
        const query = $(this).val();

        searchTimeout = setTimeout(() => {
            if (query.length >= 3 || query.length === 0) {
                searchPlatformEvents(ticketPlatform.val(), query)
                    .then(handleSearchResults)
                    .catch(handleSearchError);
            }
        }, 300);
    });

    function handleSearchResults(response) {
        resultsContainer.empty();
        if (response.events && response.events.length > 0) {
            response.events.forEach((event) => {
                const eventDate = new Date(event.date).toLocaleDateString();
                $(`<div class="cursor-pointer rounded-lg p-3 text-white hover:bg-gray-600">
                    <p class="font-medium">${event.name}</p>
                    <p class="text-sm text-gray-400">${eventDate} - ${
                    event.venue
                }</p>
                    <p class="text-xs text-gray-400">${
                        event.tickets_available
                            ? "Tickets available"
                            : "Sold out"
                    }</p>
                </div>`)
                    .on("click", () => handleEventSelection(event))
                    .appendTo(resultsContainer);
            });
        } else {
            resultsContainer.html(
                '<p class="text-gray-400 p-3">No events found</p>'
            );
        }
    }

    function handleSearchError(error) {
        console.error("Error searching events:", error);
        resultsContainer.html(
            '<p class="text-red-500 p-3">Error searching events</p>'
        );
    }

    async function handleEventSelection(event) {
        try {
            // Store platform event data
            platformEventId.val(event.id);
            platformEventUrl.val(event.url);

            // Update platform display
            $("#platformName").text(
                ticketPlatform.find("option:selected").text()
            );
            $("#eventSource").removeClass("hidden");

            // Populate event details
            $("#event_name").val(event.name);
            $("#event_start_time").val(event.start_time);
            $("#event_end_time").val(event.end_time);
            $("#ticket_url").val(event.url);
            $("#event_description").val(event.description);

            // Handle venue import
            if (event.venue) {
                const venue = await importVenue(event, dashboardType);
                $("#venue_name").val(venue.name);
                $("#venue_id").val(venue.id);
            }

            // Update UI
            modal.addClass("hidden");
            const importButton = $("#importEventButton");
            importButton.text("Change Platform Event");
        } catch (error) {
            console.error("Error importing event:", error);
            showFailureNotification("Error importing event details");
        }
    }

    // Close modal handler
    $(".close-modal").on("click", () => modal.addClass("hidden"));
}
