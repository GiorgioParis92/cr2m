self.addEventListener('push', function (event) {
    console.log('Push event received:', event);
    const data = event.data.json();
    // Display the notification
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            data: data.data,
            actions: data.actions,
        })
    );
});
