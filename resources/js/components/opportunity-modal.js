//@ts-nocheck

import $ from "jquery";
import {
    showScheduledNotification,
    showFailureNotification,
} from "../utils/swal.js";

export function initOpportunityModal() {
    try {
        // Get dashboard type from URL or element
        let dashboardType;
        const dashboardElement = document.querySelector("#dashboard_type");

        if (dashboardElement && dashboardElement instanceof HTMLInputElement) {
            dashboardType = dashboardElement.value;
        } else {
            // Try to get from URL if not in element
            const urlParts = window.location.pathname.split("/");
            const profileIndex = urlParts.indexOf("profile");
            if (profileIndex !== -1 && urlParts[profileIndex + 1]) {
                dashboardType = urlParts[profileIndex + 1];
            } else {
                dashboardType = "default";
            }
        }

        const modal = $("#opportunityModal");

        // Initialize elements
        const serviceWantedModal = $("#opportunityModal");
        const openModalBtn = $("#createOpportunityBtn");
        const closeModalBtns = $(".close-modal");
        const opportunityType = $("#opportunityType");
        const dynamicFields = $("#dynamicFields");
        const opportunityForm = $("#opportunityForm");
        const createListingBtn = $("#createListingBtn");

        // Handle prefill event
        modal.on("prefill-opportunity", function (e, eventData) {
            modal.removeClass("hidden").css("display", "block");

            // Store eventData for later use
            modal.data("eventData", eventData);

            if (eventData) {
                const excludedIds = [];
                const excludedIdField = $("#excluded_entities");

                // Collect all band IDs that should be excluded
                if (eventData.headlinerId) {
                    excludedIds.push(eventData.headlinerId);
                }
                if (eventData.mainSupportId) {
                    excludedIds.push(eventData.mainSupportId);
                }
                if (eventData.openerId) {
                    excludedIds.push(eventData.openerId);
                }

                // Handle additional band IDs if they exist
                if (eventData.bandsIds) {
                    const additionalBands = eventData.bandsIds
                        .split(",")
                        .filter((id) => id.trim());
                    excludedIds.push(...additionalBands);
                }

                // Set the excluded IDs to the field
                if (excludedIds.length > 0) {
                    console.log("Setting excluded IDs:", excludedIds);
                    excludedIdField.val(excludedIds.join(","));
                }
            }

            $("#opportunityType").val("artist_wanted").trigger("change");
        });

        // Handle time inputs directly
        $("#performance_start_time, #performance_end_time").on(
            "change",
            function () {
                console.log("Time changed, calculating set length");
                calculateSetLength();
            }
        );

        function openModal() {
            modal.removeClass("hidden");
            $("body").addClass("modal-open");
        }

        function closeModal() {
            modal.addClass("hidden").css("display", "none");
            $("body").removeClass("modal-open");
            $("#opportunityForm")[0].reset();
        }

        // Open modal
        openModalBtn.on("click", function (e) {
            e.preventDefault();
            openModal();
        });

        // Close modal handlers
        closeModalBtns.on("click", function (e) {
            e.preventDefault();
            closeModal();
        });

        // Close on backdrop click
        modal.on("click", function (e) {
            if ($(e.target).closest(".modal-content").length === 0) {
                closeModal();
            }
        });

        // Close on escape key
        $(document).on("keydown", function (e) {
            if (e.key === "Escape" && !modal.hasClass("hidden")) {
                closeModal();
            }
        });

        function getFormData() {
            // Get genres from main event form
            const selectedGenres = $(
                '#eventForm input[name="genres[]"]:checked'
            )
                .map(function () {
                    return $(this).val();
                })
                .get();

            return {
                genres: selectedGenres,
                event_date: $("#event_date").val(),
                event_start_time: $("#event_start_time").val(),
                event_end_time: $("#event_end_time").val(),
                venue_id: $("#venue_id").val(),
                headliner_id: $("#headliner_id").val(),
                main_support_id: $("#main_support_id").val(),
                opener_id: $("#opener_id").val(),
                bands_ids: $("#bands_ids").val(),
            };
        }

        // Handle opportunity type changes
        opportunityType.on("change", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const type = $(this).val();
            if (!type) {
                dynamicFields.empty();
                return false;
            }

            console.log(type);

            const formData = getFormData();

            $.ajax({
                url: `/dashboard/${dashboardType}/opportunities/type/${type}/fields`,
                method: "GET",
                data: formData,
                success: function (response) {
                    dynamicFields.html(response.html);

                    // Initialize time handlers after loading artist template
                    if (type === "artist_wanted") {
                        initializeArtistTimePickers();

                        // Get the stored excluded IDs and populate after template is loaded
                        const eventData = modal.data("eventData");
                        if (eventData) {
                            const excludedIds = [];

                            if (eventData.headlinerId)
                                excludedIds.push(eventData.headlinerId);
                            if (eventData.mainSupportId)
                                excludedIds.push(eventData.mainSupportId);
                            if (eventData.openerId)
                                excludedIds.push(eventData.openerId);

                            if (eventData.bandsIds) {
                                const additionalBands = eventData.bandsIds
                                    .split(",")
                                    .filter((id) => id.trim());
                                excludedIds.push(...additionalBands);
                            }

                            if (excludedIds.length > 0) {
                                console.log(
                                    "Setting excluded IDs after template load:",
                                    excludedIds
                                );
                                $("#excluded_entities").val(
                                    excludedIds.join(",")
                                );
                            }
                        }
                    }
                },
                error: function (error) {
                    console.error("AJAX error:", error);
                    showFailureNotification("Error loading fields");
                },
            });
        });

        function initializeArtistTimePickers() {
            console.log("Initializing time handlers");

            // Initialize flatpickr with consistent options
            const timePickerOptions = {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                onChange: function (selectedDates, dateStr, instance) {
                    // Force immediate calculation after any time change
                    calculateSetLength();
                },
            };

            // Initialize both pickers and store references
            const startPicker = flatpickr(
                "#performance_start_time",
                timePickerOptions
            );
            const endPicker = flatpickr(
                "#performance_end_time",
                timePickerOptions
            );

            // Add direct change event listeners as backup
            $("#performance_start_time, #performance_end_time").on(
                "change",
                function () {
                    console.log("Direct time change detected");
                    calculateSetLength();
                }
            );
        }

        function calculateSetLength() {
            console.log("Calculating set length");
            const startTimeInput = document.querySelector(
                "#performance_start_time"
            );
            const endTimeInput = document.querySelector(
                "#performance_end_time"
            );

            if (!startTimeInput || !endTimeInput) {
                console.log("Time inputs not found");
                return;
            }

            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            console.log("Times:", { startTime, endTime });

            if (!startTime || !endTime) {
                console.log("Missing times");
                return;
            }

            // Create Date objects for today's date
            const today = new Date().toISOString().split("T")[0];
            const start = new Date(`${today}T${startTime}`);
            const end = new Date(`${today}T${endTime}`);

            // Handle overnight performances
            if (end < start) {
                end.setDate(end.getDate() + 1);
            }

            const diff = end.getTime() - start.getTime();
            const minutes = Math.floor(diff / (1000 * 60));
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;

            let setLength = "";
            if (hours > 0) {
                setLength += `${hours} hour${hours > 1 ? "s" : ""} `;
            }
            if (remainingMinutes > 0 || hours === 0) {
                setLength += `${remainingMinutes} minute${
                    remainingMinutes !== 1 ? "s" : ""
                }`;
            }

            console.log("Set length calculated:", setLength);

            // Update ALL set length fields
            const setLengthFields = document.querySelectorAll("#set_length");
            setLengthFields.forEach((field) => {
                if (field) {
                    field.value = setLength.trim();
                    // Trigger change event
                    field.dispatchEvent(new Event("change", { bubbles: true }));
                }
            });

            // Also update hidden set_length field in the form if it exists
            const hiddenSetLength = document.querySelector(
                'input[name="set_length"]'
            );
            if (hiddenSetLength) {
                hiddenSetLength.value = setLength.trim();
            }
        }

        createListingBtn.on("click", function (e) {
            e.preventDefault();
            console.log("Create listing button clicked");

            handleCreateListing(e, closeModal);
        });
    } catch (error) {
        console.error("Error initializing opportunity modal:", error);
    }
}

export function handleCreateListing(e) {
    e.preventDefault();
    console.log("Create listing button clicked");

    const opportunityForm = $("#opportunityForm");
    if (!opportunityForm.length) {
        console.error("Form not found");
        return;
    }

    const formElement = opportunityForm.get(0);
    if (!(formElement instanceof HTMLFormElement)) {
        console.error("Invalid form element");
        return;
    }

    const formData = new FormData(formElement);
    const opportunityData = {
        type: formData.get("type"),
        position_type: formData.get("position_type"),
        main_genres: Array.from(formData.getAll("main_genres[]")),
        subgenres: {},
        performance_start_time: formData.get("performance_start_time"),
        performance_end_time: formData.get("performance_end_time"),
        set_length: formData.get("set_length"),
        poster_type: formData.get("poster_type") || "event", // Add default
        additional_requirements: formData.get("additional_requirements"),
        excluded_entities:
            formData.get("excluded_entities")?.split(",").filter(Boolean) || [],
        application_deadline: formData.get("application_deadline"), // Add this field
    };

    // Handle subgenres
    for (let [key, value] of formData.entries()) {
        if (key.includes("subgenres[")) {
            const genre = key.match(/subgenres\[(.*?)\]/)[1];
            if (!opportunityData.subgenres[genre]) {
                opportunityData.subgenres[genre] = [];
            }
            opportunityData.subgenres[genre].push(value);
        }
    }

    try {
        // Get the existing opportunities
        const mainForm = $("#eventForm");
        const opportunitiesInput = mainForm.find(
            'input[name="pending_opportunities"]'
        );
        const opportunities = JSON.parse(opportunitiesInput.val() || "[]");

        // Add new opportunity to array
        opportunities.push(opportunityData);

        // Update the hidden input with new opportunities array
        opportunitiesInput.val(JSON.stringify(opportunities));

        // Close modal and reset form
        $("#opportunityModal").addClass("hidden");
        opportunityForm[0].reset();

        showScheduledNotification(
            "Opportunity will be created when event is saved"
        );
    } catch (e) {
        console.error("Error updating opportunities:", e);
        showFailureNotification("Error adding opportunity");
    }
}
