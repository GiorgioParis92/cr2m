self.addEventListener('push', function(event) {
    console.log('Push event received:', event);
    console.log('Service Worker (Version 1.0.1) is running.');

    let data = { title: 'Default Title', body: 'Default body' };
    if (event.data) {
        try {
            data = event.data.json();
            console.log('Parsed JSON data:', data);
        } catch (e) {
            console.error('Error parsing JSON:', e);
            // Fallback to text data if JSON parsing fails
            data = {
                title: 'Notification',
                body: event.data.text()
            };
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/default-icon.png',
        data: data.url || '/',
        actions: data.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data)
    );
});

self.addEventListener('install', function(event) {
    self.skipWaiting();
});
self.addEventListener('activate', function(event) {
    event.waitUntil(
        self.clients.claim()
    );
});

