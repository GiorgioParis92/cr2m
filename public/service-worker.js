self.addEventListener('push', function(event) {
    console.log('Push event received:', event);

    let data = { title: 'Default Title', body: 'Default body' };
    if (event.data) {
        try {
            data = event.data.json();
            console.log('Parsed JSON data:', data);
        } catch (e) {
            console.error('Error parsing JSON:', e);
            // If JSON parsing fails, use text data
            data = {
                title: 'Notification',
                body: event.data.text()
            };
            console.log('Text data:', data.body);
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon || '/default-icon.png',
            data: data.data || '/',
            actions: data.actions || []
        })
    );
});
