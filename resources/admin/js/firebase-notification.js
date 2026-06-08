"use strict";

import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import moment from "moment";

// Use async function to handle the await properly
async function initializeFirebase() {
    try {
        const firebaseConfig = await getClientConfig();

        if (firebaseConfig?.vapidKey) {
            // Initialize Firebase
            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);

            // Request Notification Permission
            const permission = await Notification.requestPermission();
            if (permission === "granted") {
                try {
                    // Get FCM token and subscribe to topic
                    const currentToken = await getToken(messaging, {
                        vapidKey: firebaseConfig.vapidKey,
                    });

                    if (currentToken) {
                        // Subscribe to the global topic
                        subscribeToTopic(currentToken);
                    } else {
                        console.log("No registration token available.");
                    }
                } catch (err) {
                    console.warn("Error retrieving token:", err);
                }
            } else {
                console.warn("Notification permission denied.");
            }

            // Listen for incoming messages (foreground)
            onMessage(messaging, (payload) => {
                if (payload.notification) {
                    // Create browser notification
                    const notification = new Notification(payload.notification.title, {
                        body: payload.notification.body,
                    });

                    renderNotification({
                        ...payload.notification,
                        created_at: new Date(),
                    });

                    // You can also add click handling if needed
                    notification.onclick = function () {
                        window.focus();
                        this.close();
                    };
                }
            });
        }
    } catch (error) {
        console.warn("Error initializing Firebase");
    }
}

async function getClientConfig() {
    try {
        const response = await fetch("/firebase/cloud-messaging.json");
        return await response.json();
    } catch (error) {
        return null;
    }
}

function renderNotification(data) {
    const notificationContainer = document.querySelector("#notification-list-container");
    const notificationCount = document.querySelector("#notification-count");

    const notificationElement = `<div
                                class="flex cursor-pointer gap-2 rounded-md p-2 duration-300 bg-primary/5 hover:bg-primary/10 dark:hover:bg-primary/20">
                                <div class="text-sm">
                                    <div class="flex gap-1">
                                        <span>${data?.body}</span>
                                    </div>
                                    <span
                                        class="text-xs text-n100 dark:text-n50">${moment(data.created_at).fromNow()}</span>
                                </div>
                            </div>`;

    notificationContainer.insertAdjacentHTML("afterbegin", notificationElement);

    const oldCount = parseInt(notificationCount.textContent) || 0;
    notificationCount.textContent = oldCount + 1;
}

// Function to subscribe to a topic
function subscribeToTopic(token) {
    fetch(`/admin/settings/notification/token-update`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content"),
        },
        body: JSON.stringify({
            token: token,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => console.log("Topic subscription result:", data))
        .catch((error) => console.warn("Something went wrong."));
}

// Call the initialization function
initializeFirebase();
