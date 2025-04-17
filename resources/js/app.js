// Import dependencies
import Alpine from "alpinejs";
import "summernote/dist/summernote-bs4.css";
import $ from "jquery";

// Import components and utilities
import { initOpportunityModal } from "../js/components/opportunity-modal.js";
import { initAccordion } from "./components/accordion.js";
import { initRatings } from "./components/ratings.js";
import { initCalendar } from "./components/calendar.js";
import { initEventForm } from "./forms/event-form.js";
import { updateModuleStatus } from "./settings/modules.js";
import { formatCurrency, formatDateToDMY } from "./utils/formatters";
import { initIpTracker } from "./utils/ip-tracker";
import { initializeSummernote } from "./components/summernote";
import {
    initializePasswordChecker,
    togglePasswordVisibility,
} from "./utils/password-checker.js";

// Initialize Alpine.js data and plugins
document.addEventListener("alpine:init", () => {
    Alpine.data("passwordVisibility", () => ({
        showPassword: false,
        init() {
            if (typeof initializePasswordChecker === "function") {
                initializePasswordChecker("password");
            }
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
            const input = document.getElementById("password");
            if (input) {
                input.type = this.showPassword ? "text" : "password";
            }
        },
    }));
});

// Make all functions globally available
Object.assign(window, {
    initializePasswordChecker,
    togglePasswordVisibility,
    updateModuleStatus,
    formatCurrency,
    formatDateToDMY,
    initializeSummernote,
    initOpportunityModal,
    initAccordion,
    initRatings,
    initCalendar,
    initIpTracker,
    initEventForm,
});

// Start Alpine.js
Alpine.start();

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

    try {
        initOpportunityModal();
    } catch (error) {
        console.error("Error in opportunity modal:", error);
    }
});
