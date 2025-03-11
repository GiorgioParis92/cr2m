
require('./bootstrap');
import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function () {
    if ('serviceWorker' in navigator && 'Notification' in window) {
        navigator.serviceWorker.register('/service-worker.js', { scope: '/' })
            .then(function (registration) {
                console.log('Service Worker Registered with scope:', registration.scope);
                return navigator.serviceWorker.ready;
            })
            .then(function (registration) {
                console.log('Service Worker Ready');

                const enableNotificationsButton = document.getElementById('enable-notifications');
                enableNotificationsButton.style.display = 'block';

                enableNotificationsButton.addEventListener('click', function () {
                    enableNotificationsButton.disabled = true;

                    Notification.requestPermission().then(function (permission) {
                        if (permission === 'granted') {
                            console.log('Notification permission granted.');
                            subscribeUserToPush();
                        } else {
                            console.warn('Notification permission denied');
                            enableNotificationsButton.disabled = false;
                        }
                    });
                });
            })
            .catch(function (error) {
                console.error('Service Worker registration failed:', error);
            });
    }
});

function subscribeUserToPush() {
    navigator.serviceWorker.ready.then(function (registration) {
        const applicationServerKey = urlBase64ToUint8Array('BFbUWF-kOLUzkZ1JAlHVhOlJMjSNBbUk4ZNDDWdsrjPrCAY3k4H-nFUm39QBFjTZsV9F--ONGybs6wuXhOJpdDU');
        registration.pushManager.getSubscription()
            .then(function (subscription) {
                if (subscription) {
                    console.log('User is already subscribed:', subscription);
                    return subscription;
                }

                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            })
            .then(function (subscription) {
                console.log('User is subscribed:', subscription);

                // Send subscription to the server
                const subscriptionData = subscription.toJSON();
                subscriptionData.content_encoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

                return axios.post('/api/save-subscription', subscriptionData);
            })
            .then(function () {
                console.log('Subscription sent to server.');
            })
            .catch(function (error) {
                console.error('Failed to subscribe the user:', error);
            });
    });
}

// Utility function to convert VAPID key
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

