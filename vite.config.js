import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    resolve: {
        modules: ["node_modules"],
        alias: {
            $: "jquery", // Ensure jQuery is aliased globally to $
            jquery: "jquery", // Alias jQuery to its actual package location
        },
    },
    build: {
        rollupOptions: {
            // external: ["summernote/dist/summernote-lite.js"], // External resources
        },
    },
});
