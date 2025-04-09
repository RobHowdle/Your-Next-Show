/**
 * Handles icon state updates for rating stars
 * @param {HTMLElement} label - The rating label element
 * @param {string} state - The state to set ('empty', 'full', or 'hot')
 */
function updateRatingIcon(label, state) {
    const currentImage = label.style.backgroundImage;
    const newImage = currentImage.replace(
        /empty\.png|full\.png|hot\.png/,
        `${state}.png`
    );
    label.style.backgroundImage = newImage;
}

/**
 * Handles the rating mouseleave event
 * @param {HTMLElement} group - The rating group container
 * @param {Array<HTMLElement>} labels - Collection of rating label elements
 */
function handleRatingLeave(group, labels) {
    const selectedInput = group.querySelector("input:checked");

    if (!selectedInput || !(selectedInput instanceof HTMLInputElement)) {
        labels.forEach((label) => updateRatingIcon(label, "empty"));
        return;
    }

    const selectedValue = parseInt(selectedInput.value);
    labels.forEach((label, index) => {
        if (index + 1 <= selectedValue) {
            updateRatingIcon(label, selectedValue === 5 ? "hot" : "full");
        } else {
            updateRatingIcon(label, "empty");
        }
    });
}

/**
 * Handles the rating change event
 * @param {HTMLInputElement} input - The rating input element
 * @param {NodeListOf<HTMLElement>} labels - Collection of rating label elements
 */
function handleRatingChange(input, labels) {
    const value = parseInt(input.value);

    labels.forEach((label, index) => {
        if (index + 1 <= value) {
            updateRatingIcon(label, value === 5 ? "hot" : "full");
        } else {
            updateRatingIcon(label, "empty");
        }
    });
}

/**
 * Handles the rating hover effect
 * @param {HTMLInputElement} input - The rating input element
 * @param {NodeListOf<HTMLElement>} labels - Collection of rating label elements
 */
function handleRatingHover(input, labels) {
    const value = parseInt(input.value);

    labels.forEach((label, index) => {
        if (index + 1 <= value) {
            updateRatingIcon(label, value === 5 ? "hot" : "full");
        }
    });
}

/**
 * Initializes rating functionality
 */
export function initRatings() {
    const ratingGroups = document.querySelectorAll(".rating");

    ratingGroups.forEach((group) => {
        if (!(group instanceof HTMLElement)) return;

        const inputs = group.querySelectorAll('input[type="radio"]');
        const labels = Array.from(
            group.querySelectorAll(".rating-label")
        ).filter((el) => el instanceof HTMLElement);

        inputs.forEach((input) => {
            if (!(input instanceof HTMLInputElement)) return;

            // Handle rating change
            input.addEventListener("change", () => {
                const value = parseInt(input.value);
                labels.forEach((label, index) => {
                    if (index + 1 <= value) {
                        updateRatingIcon(label, value === 5 ? "hot" : "full");
                    } else {
                        updateRatingIcon(label, "empty");
                    }
                });
            });

            // Handle hover effects
            const label = input.nextElementSibling;
            if (label instanceof HTMLElement) {
                label.addEventListener("mouseenter", () => {
                    const value = parseInt(input.value);
                    labels.forEach((hoverLabel, index) => {
                        if (index + 1 <= value) {
                            updateRatingIcon(
                                hoverLabel,
                                value === 5 ? "hot" : "full"
                            );
                        }
                    });
                });

                label.addEventListener("mouseleave", () => {
                    handleRatingLeave(group, labels);
                });
            }
        });
    });
}
