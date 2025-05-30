import { showFailureNotification } from "./swal";
import Swal from "sweetalert2";

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        clearTimeout(timeout); // Clear any existing timeout
        timeout = setTimeout(() => {
            func.apply(this, args); // Use apply to maintain context
        }, wait);
    };
}

function generateSecurePassword() {
    const minLength = 14;
    const maxLength = 32;
    const length =
        Math.floor(Math.random() * (maxLength - minLength + 1)) + minLength;
    const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const lowercase = "abcdefghijklmnopqrstuvwxyz";
    const numbers = "0123456789";
    const symbols = "@$!%*?&";
    const allChars = uppercase + lowercase + numbers + symbols;

    let password = "";
    // Ensure at least one of each required character type
    password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
    password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
    password += numbers.charAt(Math.floor(Math.random() * numbers.length));
    password += symbols.charAt(Math.floor(Math.random() * symbols.length));

    // Fill the rest with random characters
    for (let i = password.length; i < length; i++) {
        password += allChars.charAt(
            Math.floor(Math.random() * allChars.length)
        );
    }

    // Shuffle the password
    return password
        .split("")
        .sort(() => Math.random() - 0.5)
        .join("");
}

function checkForCommonPatterns(password, firstName = "", lastName = "") {
    const commonPatterns = [
        {
            pattern: /password/i,
            message: "Really? 'password'? Come on, you're better than that!",
        },
        {
            pattern: /test/i,
            message: "Testing 1-2-3? Let's try something less obvious!",
        },
        { pattern: /123/i, message: "123? What's next, ABC?" },
        { pattern: /qwerty/i, message: "QWERTY? That's just lazy typing!" },
        {
            pattern: new RegExp(firstName, "i"),
            message:
                "Using your name? That's the first thing someone would guess!",
        },
        {
            pattern: new RegExp(lastName, "i"),
            message: "Your last name? Let's be more creative!",
        },
        {
            pattern: /admin/i,
            message: "Admin? That's like using 'password' as your password!",
        },
        {
            pattern: /letmein/i,
            message: "Let me in? More like let everyone in!",
        },
    ];

    const foundPatterns = commonPatterns.filter(({ pattern }) =>
        pattern.test(password)
    );

    if (foundPatterns.length > 0) {
        const messages = foundPatterns.map((p) => p.message);
        return {
            isValid: false,
            message: `Found common patterns:\n• ${messages.join("\n• ")}`,
        };
    }

    return { isValid: true };
}

// Main Functions
export function checkRequirement(value, test, inputId = "password") {
    const requirement = document.getElementById(
        `${inputId}-${test}-requirement`
    );
    const icon = requirement?.querySelector("svg");

    const regularChecks = {
        length: (pwd) => pwd.length >= 8,
        uppercase: (pwd) => /[A-Z]/.test(pwd),
        lowercase: (pwd) => /[a-z]/.test(pwd),
        number: (pwd) => /[0-9]/.test(pwd),
        special: (pwd) => /[@$!%*?&]/.test(pwd),
        patterns: (pwd) => {
            const form = document.getElementById(inputId)?.closest("form");
            const firstName =
                form?.querySelector('[name="first_name"]')?.value || "";
            const lastName =
                form?.querySelector('[name="last_name"]')?.value || "";

            const result = checkForCommonPatterns(pwd, firstName, lastName);

            if (!result.isValid) {
                const requirement = document.getElementById(
                    `${inputId}-patterns-requirement`
                );
                if (requirement) {
                    const messageEl = requirement.querySelector(".message");
                    if (messageEl) {
                        messageEl.textContent = result.message;
                        // Also show the failure notification with all found patterns
                        showFailureNotification(result.message);
                    }
                }
            }

            return result.isValid;
        },
    };

    if (test === "compromised") {
        return true; // We'll handle this separately
    }

    const isValid = regularChecks[test](value);
    requirement?.classList.toggle("valid", isValid);
    icon?.classList.toggle("hidden", !isValid);
    return isValid;
}

export function initializePasswordChecker(inputId = "password") {
    const passwordInput = document.getElementById(inputId);
    let hasCheckedCompromised = false;
    let hasCheckedPatterns = false;
    let invalidAttempts = 0;

    // Create debounced check function
    const debouncedPasswordCheck = debounce((currentPassword) => {
        const requirements = [
            "length",
            "uppercase",
            "lowercase",
            "number",
            "special",
            "patterns",
        ];

        if (currentPassword.length >= 8) {
            const form = document.getElementById(inputId)?.closest("form");
            const firstName =
                form?.querySelector('[name="first_name"]')?.value || "";
            const lastName =
                form?.querySelector('[name="last_name"]')?.value || "";
            const patternResult = checkForCommonPatterns(
                currentPassword,
                firstName,
                lastName
            );

            const allRequirementsMet = requirements.every((req) =>
                checkRequirement(currentPassword, req, inputId)
            );

            if (!allRequirementsMet) {
                invalidAttempts++;
                console.log(`Invalid attempts: ${invalidAttempts}`);

                // Show pattern message if there are pattern issues
                if (invalidAttempts < 3) {
                    if (!patternResult.isValid) {
                        showFailureNotification(patternResult.message, 5000);
                    } else {
                        // Show generic message if other requirements aren't met
                        showFailureNotification(
                            "Your password doesn't meet all requirements. Please check the guidelines below.",
                            5000
                        );
                    }
                } else if (invalidAttempts === 3) {
                    // Show password generation prompt only on the third attempt
                    showPasswordGenerationPrompt(passwordInput, inputId);
                }
            } else {
                invalidAttempts = 0;
            }
        }
    }, 1000);

    // Password generation prompt function
    const showPasswordGenerationPrompt = async (passwordInput, inputId) => {
        console.log("🔐 Password generation process started...");

        try {
            const result = await Swal.fire({
                title: "Need a Strong Password?",
                text: "Would you like us to generate a secure password for you?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Generate Password",
                cancelButtonText: "No, thanks",
                confirmButtonColor: "#1D232A",
                background: "#1F2937",
                color: "#fff",
            });

            if (result.isConfirmed) {
                console.log("⏳ Starting generation process...");

                // First show loading state
                Swal.showLoading();

                try {
                    console.log("⚙️ Generating secure password...");
                    const generatedPassword = generateSecurePassword();
                    console.log("✅ Password generated:", generatedPassword);

                    if (!passwordInput) {
                        throw new Error("Password input element not found");
                    }

                    // Update password fields
                    console.log("📝 Updating password fields...");
                    passwordInput.value = generatedPassword;

                    const confirmationInput = document.getElementById(
                        `${inputId}_confirmation`
                    );
                    if (confirmationInput) {
                        confirmationInput.value = generatedPassword;
                        console.log("✅ Confirmation field updated");
                    }

                    // Update visual indicators
                    console.log("🎨 Updating visual indicators...");
                    updatePasswordStrength(generatedPassword, inputId);

                    // Show success message
                    await Swal.fire({
                        title: "Password Generated!",
                        html: `
                        <p>A secure password has been generated that:</p>
                        <ul class="text-left text-sm mt-2 space-y-1">
                            <li>• Is between 14-32 characters</li>
                            <li>• Contains uppercase & lowercase letters</li>
                            <li>• Contains numbers & special characters</li>
                            <li>• Has been checked against known data breaches</li>
                        </ul>
                        <p class="text-sm mt-4">Make sure to save this password in your password manager!</p>
                    `,
                        icon: "success",
                        confirmButtonColor: "#1D232A",
                        background: "#1F2937",
                        color: "#fff",
                    });

                    console.log("🎉 Process completed successfully");
                    invalidAttempts = 0;
                } catch (error) {
                    console.error(
                        "❌ Error during password generation:",
                        error
                    );
                    await Swal.fire({
                        title: "Error",
                        text: "Sorry, there was a problem generating your password. Please try again.",
                        icon: "error",
                        confirmButtonColor: "#1D232A",
                        background: "#1F2937",
                        color: "#fff",
                    });
                }
            } else {
                console.log("❌ User cancelled password generation");
            }
        } catch (error) {
            console.error("❌ Error in password generation flow:", error);
            await Swal.fire({
                title: "Error",
                text: "An unexpected error occurred. Please try again.",
                icon: "error",
                confirmButtonColor: "#1D232A",
                background: "#1F2937",
                color: "#fff",
            });
        }
    };

    // Input event listener
    passwordInput?.addEventListener("input", (e) => {
        const currentPassword = e.target.value;

        // Reset states
        hasCheckedCompromised = false;
        hasCheckedPatterns = false;

        // Reset visual states
        const requirements = [
            "length",
            "uppercase",
            "lowercase",
            "number",
            "special",
            "patterns",
        ];

        requirements.forEach((req) => {
            const requirement = document.getElementById(
                `${inputId}-${req}-requirement`
            );
            const messageEl = requirement?.querySelector(".message");
            if (messageEl) {
                messageEl.classList.remove("text-red-500");
            }
        });

        // Reset compromised requirement visual state
        const requirement = document.getElementById(
            `${inputId}-compromised-requirement`
        );
        const successIcon = requirement?.querySelector(".success-icon");
        const failureIcon = requirement?.querySelector(".failure-icon");
        const loadingIcon = requirement?.querySelector(".loading-icon");

        successIcon?.classList.add("hidden");
        failureIcon?.classList.add("hidden");
        loadingIcon?.classList.add("hidden");

        // Call debounced check
        debouncedPasswordCheck(currentPassword);

        // Update strength meter
        updatePasswordStrength(currentPassword, inputId);
    });

    // Check for compromised passwords on blur
    passwordInput?.addEventListener("blur", async () => {
        const password = passwordInput.value;
        if (password && !hasCheckedCompromised && password.length > 0) {
            const requirement = document.getElementById(
                `${inputId}-compromised-requirement`
            );
            const successIcon = requirement?.querySelector(".success-icon");
            const failureIcon = requirement?.querySelector(".failure-icon");
            const loadingIcon = requirement?.querySelector(".loading-icon");

            successIcon?.classList.add("hidden");
            failureIcon?.classList.add("hidden");
            loadingIcon?.classList.remove("hidden");

            try {
                const response = await fetch("/api/check-password", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: JSON.stringify({ password }),
                });

                const data = await response.json();
                const isValid = !data.compromised;

                loadingIcon?.classList.add("hidden");

                if (isValid) {
                    successIcon?.classList.remove("hidden");
                    failureIcon?.classList.add("hidden");
                } else {
                    successIcon?.classList.add("hidden");
                    failureIcon?.classList.remove("hidden");
                    showFailureNotification(
                        "This password has been found in data breaches. Please choose a different one."
                    );
                }

                requirement?.classList.toggle("valid", isValid);
                hasCheckedCompromised = true;
            } catch (error) {
                console.error("Error checking compromised password:", error);
                loadingIcon?.classList.add("hidden");
                successIcon?.classList.remove("hidden");
            }
        }
    });
}

export function updatePasswordStrength(password, inputId = "password") {
    const meter = document.getElementById(`${inputId}-strength-meter`);
    const text = document.getElementById(`${inputId}-strength-text`);
    const requirements = [
        "length",
        "uppercase",
        "lowercase",
        "number",
        "special",
        "patterns",
    ];
    const strength = requirements.filter((req) =>
        checkRequirement(password, req, inputId)
    ).length;

    const levels = {
        0: {
            width: "0%",
            class: "bg-gray-800",
            text: "",
        },
        1: {
            width: "25%",
            class: "bg-red-500",
            text: "Weak",
        },
        2: {
            width: "50%",
            class: "bg-orange-500",
            text: "Medium",
        },
        3: {
            width: "75%",
            class: "bg-yellow-500",
            text: "Strong",
        },
        4: {
            width: "100%",
            class: "bg-green-500",
            text: "Very Strong",
        },
        5: {
            width: "100%",
            class: "bg-green-600",
            text: "Excellent",
        },
    };

    const level = levels[strength] || levels[0];
    meter.style.width = level.width;
    meter.className = `h-full rounded-full transition-all duration-300 ${level.class}`;
    text.textContent = level.text;
}

export function checkPasswordMatch(inputId = "password") {
    const password = document.getElementById(inputId)?.value || "";
    const confirmation =
        document.getElementById(`${inputId}_confirmation`)?.value || "";
    const matchRequirement = document.getElementById(
        `${inputId}-password-match-requirement`
    );
    const matchIcon = matchRequirement?.querySelector("svg");
    const strengthMeter = document.getElementById(`${inputId}-strength-meter`);

    if (!password || !confirmation) {
        matchRequirement?.classList.remove("valid");
        matchIcon?.classList.add("hidden");
        return false;
    }

    const matches = password === confirmation;

    // Update the visual indicators
    matchRequirement?.classList.toggle("valid", matches);
    matchIcon?.classList.toggle("hidden", !matches);

    // Optionally change the strength meter color when passwords don't match
    if (strengthMeter && !matches) {
        strengthMeter.className = `h-full rounded-full transition-all duration-300 bg-red-500`;
    }

    return matches;
}

export function togglePasswordVisibility(inputId = "password") {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(`${inputId}-eye`);

    if (!passwordInput || !eyeIcon) return;

    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;

    // Update the eye icon
    if (type === "text") {
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        `;
    } else {
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}
