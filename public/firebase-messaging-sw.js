importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

const getFirebaseConfig = async () => {
    const response = await fetch("/storage/firebase/cloud-messaging.json");
    return await response.json(); // Note: you should return the result of response.json()
}

async function initFirebase() {
    const firebaseConfig = await getFirebaseConfig()
    // Your Firebase configuration - must match what you use in your main app
    if(firebaseConfig && firebaseConfig?.vapidKey) {
        firebase.initializeApp(firebaseConfig);

        // You can leave this empty if you only need foreground notifications
        firebase.messaging();
    }
}

initFirebase();
