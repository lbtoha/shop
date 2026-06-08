import tailwindcss from "@tailwindcss/vite";
import laravel, { refreshPaths } from "laravel-vite-plugin";
import path from "path";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // storefront (public shop)
                "resources/shop/css/app.css",
                "resources/shop/js/app.js",

                // admin
                "resources/admin/css/app.css",
                "resources/shared/css/icon-picker.css",
                "resources/shared/js/primary-dashboard/icon-picker.js",
                "resources/admin/js/app.js",
                "resources/admin/js/logout.js",
                "resources/admin/js/firebase-notification.js",
                "resources/admin/js/settings/page-sections/team.js",
                "resources/shared/js/editor.js",
                "resources/shared/css/editor.css",
                "resources/admin/js/contest/prize-list.js",
                "resources/admin/js/game/game.js",

                // pages
                "resources/admin/js/dashboard/index.js",
                "resources/admin/js/settings/pwa.js",
                "resources/admin/js/manage-user/send-notification.js",
                "resources/admin/js/settings/edit-frontend-pages.js",
                "resources/admin/js/settings/general.js",
                "resources/admin/js/settings/kyc-form.js",
                "resources/admin/js/settings/maintenance.js",
                "resources/admin/js/settings/menu.js",
                "resources/admin/js/settings/notifications.js",
                "resources/admin/js/settings/payment-method.js",
                "resources/admin/js/settings/robots.js",
                "resources/admin/js/settings/seo.js",
                "resources/admin/js/settings/sitemap.js",
                "resources/admin/js/settings/task-schedule.js",
                "resources/admin/js/settings/withdraw-method.js",
                "resources/admin/js/admin-user/roles.js",
                "resources/admin/js/admin-user/admin.js",
                "resources/admin/js/admin-user/profile-edit.js",
                "resources/admin/js/settings/page-sections/dynamic-sections.js",
                "resources/admin/js/support-tickets/chat.js",
                "resources/admin/js/withdraw/details.js",
                "resources/admin/js/payments/details.js",
                "resources/admin/js/quiz/category.js",
                "resources/admin/js/quiz/quiz.js",
                "resources/admin/js/quiz/question-list.js",
                "resources/admin/js/contest/question-list.js",
                "resources/admin/js/quiz/bank.js",
                "resources/admin/js/badge/index.js",
                "resources/shared/css/phone.css",
                "resources/shared/js/primary-dashboard/icon-picker.js",
                "resources/admin/js/manage-user/user-details.js",
                "resources/admin/js/contest/contest.js",
                "resources/admin/js/contest/category.js",
                "resources/admin/js/question-bank/category.js",
                "resources/admin/js/settings/social-login.js",
                "resources/admin/js/ads/create.js",
                "resources/admin/js/question.js",
                "resources/admin/js/article/article.js",
                // components
                "resources/shared/js/table.js",
                "resources/shared/js/phone.js",
            ],
            refresh: [...refreshPaths],
        }),
        tailwindcss(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ["legacy-js-api"],
            },
        },
    },
    build: {
        outDir: "public/build",
        chunkSizeWarningLimit: 1000,
    },
    resolve: {
        alias: {
            $: "jQuery",
            moment: path.resolve(__dirname, "node_modules/moment/moment"),
            "@": path.resolve(__dirname, "resources/"),
        },
    },
    server: {
        watch: {
            usePolling: true, // Enable polling
            interval: 1000, // Set polling interval to 1000ms (1 second)
        },
        cors: true,
    },
});
