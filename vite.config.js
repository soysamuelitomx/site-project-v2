import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/login.css",
                "resources/css/layout.css",
                "resources/css/dashboard.css",
                "resources/css/queries.css",
                "resources/css/live.css",
                "resources/css/settings.css",
                "resources/js/app.js",
                "resources/js/login.js",
                "resources/js/layout.js",
                "resources/js/dashboard.js",
                "resources/js/queries.js",
                "resources/js/live.js",
                "resources/js/mqtt.js",
                "resources/js/settings.js",
            ],
            refresh: true,
        }),
    ],
});
