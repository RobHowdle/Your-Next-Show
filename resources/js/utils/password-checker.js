export function checkRequirement(value, test, inputId = "password") {
    const requirement = document.getElementById(
        `${inputId}-${test}-requirement`
    );
    const icon = requirement?.querySelector("svg");

    // Regular synchronous checks first
    const regularChecks = {
        length: (pwd) => pwd.length >= 8,
        uppercase: (pwd) => /[A-Z]/.test(pwd),
        lowercase: (pwd) => /[a-z]/.test(pwd),
        number: (pwd) => /[0-9]/.test(pwd),
        special: (pwd) => /[@$!%*?&]/.test(pwd),
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
    let lastCheckedPassword = "";

    // Listen for input changes
    passwordInput?.addEventListener("input", (e) => {
        const currentPassword = e.target.value;
        // Reset the compromised check if password changes
        if (currentPassword !== lastCheckedPassword) {
            hasCheckedCompromised = false;
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
        }
        updatePasswordStrength(currentPassword, inputId);
    });

    // Check for compromised password on blur
    passwordInput?.addEventListener("blur", async () => {
        const password = passwordInput.value;

        // Check if all regular requirements are met
        const requirements = [
            "length",
            "uppercase",
            "lowercase",
            "number",
            "special",
        ];
        const allRequirementsMet = requirements.every((req) =>
            checkRequirement(password, req, inputId)
        );

        if (
            allRequirementsMet &&
            !hasCheckedCompromised &&
            password.length > 0 &&
            password !== lastCheckedPassword
        ) {
            const requirement = document.getElementById(
                `${inputId}-compromised-requirement`
            );
            const successIcon = requirement?.querySelector(".success-icon");
            const failureIcon = requirement?.querySelector(".failure-icon");
            const loadingIcon = requirement?.querySelector(".loading-icon");

            // Show loading state
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

                // Hide loading spinner
                loadingIcon?.classList.add("hidden");

                // Show appropriate icon
                if (isValid) {
                    successIcon?.classList.remove("hidden");
                    failureIcon?.classList.add("hidden");
                } else {
                    successIcon?.classList.add("hidden");
                    failureIcon?.classList.remove("hidden");
                }

                requirement?.classList.toggle("valid", isValid);
                hasCheckedCompromised = true;
                lastCheckedPassword = password;

                if (!isValid) {
                    updatePasswordStrength(password, inputId);
                }
            } catch (error) {
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
