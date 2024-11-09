
require('./bootstrap');
import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';


// In your app.js or a separate JS file

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(function (registration) {
            console.log('Service Worker Registered', registration);

            registration.pushManager.getSubscription().then(function (subscription) {
                if (subscription === null) {
                    // Subscribe the user
                    registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: 'BFbUWF-kOLUzkZ1JAlHVhOlJMjSNBbUk4ZNDDWdsrjPrCAY3k4H-nFUm39QBFjTZsV9F--ONGybs6wuXhOJpdDU'
                    }).then(function (subscription) {
                        // Send subscription to the server
                        axios.post('/save-subscription', subscription.toJSON());
                    }).catch(function (err) {
                        console.error('Subscription error:', err);
                    });
                } else {
                    console.log('Already subscribed:', subscription);
                }
            });
        })
        .catch(function (err) {
            console.error('Service Worker registration failed:', err);
        });
}
