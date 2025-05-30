/**
 * Formats a number as GBP currency
 * @param {number} value - The value to format
 * @returns {string} Formatted currency string
 */
export function formatCurrency(value) {
    return new Intl.NumberFormat("en-GB", {
        style: "currency",
        currency: "GBP",
    }).format(value);
}

/**
 * Formats a date string to DD-MM-YYYY format
 * @param {string} dateString - The date to format
 * @returns {string} Formatted date string
 */
export function formatDateToDMY(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
}
