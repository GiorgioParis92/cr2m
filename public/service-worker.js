

self.addEventListener('push', function(event) {
    console.log('Push event received:', event);
    const data = event.data.json();
    console.log('Push data:', data);

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon || '/default-icon.png',
            data: data.data || '/',
            actions: data.actions || []
        })
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data)
    );
});
