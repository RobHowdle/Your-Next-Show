import $ from "jquery";

/**
 * Initializes accordion functionality
 */
export function initAccordion() {
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
}
