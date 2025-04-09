// Import dependencies
import Alpine from "alpinejs";
import "summernote/dist/summernote-bs4.css";
import $ from "jquery";

// Import components
import { initOpportunityModal } from "./components/opportunity-modal";
import { initAccordion } from "./components/accordion";
import { initRatings } from "./components/ratings";
import { initCalendar } from "./components/calendar";
import { initEventForm } from "./forms/event-form";

// Import utilities
import { formatCurrency, formatDateToDMY } from "./utils/formatters";
import { initIpTracker } from "./utils/ip-tracker";
import { initializeSummernote } from "./components/summernote";

Object.assign(window, {
    formatCurrency,
    formatDateToDMY,
    initializeSummernote,
});

// Initialize components when document is ready
$(document).ready(function () {
    const dashboardElement = document.querySelector("#dashboard_type");
    const dashboardType =
        dashboardElement instanceof HTMLInputElement
            ? dashboardElement.value
            : "default";

    if ($("#eventForm").length) {
        initEventForm({ dashboardType });
    }

    // Initialize other components
    initAccordion();
    initRatings();
    initCalendar();
    initIpTracker();
});
