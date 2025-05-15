import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ mode }) => {
    // Load env variables
    const env = loadEnv(mode, process.cwd(), "");

    return {
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                refresh: true,
            }),
            tailwindcss(),
        ],
        define: {
            // Make env variables available to the client
            "import.meta.env.VITE_PUSHER_APP_KEY": JSON.stringify(
                env.VITE_PUSHER_APP_KEY || env.PUSHER_APP_KEY
            ),
            "import.meta.env.VITE_PUSHER_APP_CLUSTER": JSON.stringify(
                env.VITE_PUSHER_APP_CLUSTER || env.PUSHER_APP_CLUSTER || "ap2"
            ),
        },
        // Show detailed build info to help with troubleshooting
        build: {
            sourcemap: mode !== "production",
        },
    };
});






