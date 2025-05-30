import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            maxWidth: {
                yns132: "132px",
                yns250: "250px",
                yns22: "22rem",
            },
            padding: {
                yns25: "25rem",
                yns22: "22rem",
            },
            fontFamily: {
                sans: ["Raleway", "sans-serif"],
                heading: ["Roboto", "sans-serif"],
            },
            colors: {
                yns_black: "#000000",
                opac_black: "rgba(0,0,0,0.4)",
                opac_5_black: "rgba(0,0,0,0.5)",
                opac_8_black: "rgba(0,0,0,0.8)",
                yns_light_gray: "rgba(217,217,217,0.5)",
                yns_light_gray1: "#D9D9D9",
                yns_red: "#EF3F38",
                yns_purple: "#9022BB",
                yns_cyan: "#29C0D2",
                yns_teal: "#53E5D6",
                yns_yellow: "#D59220",
                yns_dark_orange: "#F03F37",
                yns_dark_gray: "#1D232A",
                yns_dark_blue: "#111827",
                yns_med_gray: "#D1D5DB",
            },
            backgroundImage: {
                "gradient-button": "linear-gradient(to top, #F03F37 , #D59220)",
            },
            gap: {
                275: "2.75rem",
            },
            width: {
                400: "25rem",
            },
            height: {
                520: "32.5rem",
            },
            borderRadius: {
                50: "50px",
            },
            opacity: {
                disabled: "0.5",
            },
            cursor: {
                "not-allowed": "not-allowed",
            },
            pointerEvents: {
                none: "none",
            },
        },
        screens: {
            sm: "320px",
            // => @media (min-width: 640px) { ... }

            md: "575px",
            // => @media (min-width: 768px) { ... }

            lg: "757px",
            // => @media (min-width: 1024px) { ... }

            xl: "991px",
            // => @media (min-width: 1280px) { ... }

            "2xl": "1199px",
            // => @media (min-width: 1536px) { ... }

            "3xl": "1366px",
            // => @media (min-width: 1536px) { ... }

            "4xl": "1680px",
            // => @media (min-width: 1536px) { ... }
        },
        maxHeight: {
            "40rem": "40rem",
        },
    },

    plugins: [forms, require("daisyui")],
};
