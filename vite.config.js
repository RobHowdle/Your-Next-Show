import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
            $: "jquery",
            jquery: "jquery",
            flatpickr: path.resolve(__dirname, "node_modules/flatpickr"),
        },
    },
    optimizeDeps: {
        include: ["@fullcalendar/core", "@fullcalendar/daygrid", "flatpickr"],
    },
});
