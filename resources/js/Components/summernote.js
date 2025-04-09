/**
 * Initialize Summernote editor
 * @param {string} selector - The selector for the editor
 * @param {string} content - Initial content
 */
export function initializeSummernote(selector, content = "") {
    $(selector).summernote({
        placeholder: "Tell us about you...",
        tabsize: 2,
        height: 300,
        toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "underline", "clear"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["insert", ["link"]],
            ["view", ["fullscreen", "codeview", "help"]],
        ],
        callbacks: {
            onInit: function () {
                if (typeof content === "string" && content.length > 0) {
                    $(selector).summernote("code", content);
                }
            },
        },
    });
}
