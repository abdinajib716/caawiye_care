import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
// Removed: Module asset collection - no longer needed for healthcare application
const allPaths = ["resources/css/app.css", "resources/js/app.js"];

export default defineConfig({
    plugins: [
        laravel({
            input: allPaths,
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    esbuild: {
        jsx: "automatic",
        // drop: ['console', 'debugger'],
    },
});
