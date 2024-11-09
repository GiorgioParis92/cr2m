<div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const iconPath = '{{ asset('logo.PNG') }}';

            // Request permission if not already granted
            if (Push.Permission.has()) {
                createNotifications();
            } else {
                Push.Permission.request(() => {
                    createNotifications();
                });
            }

            function createNotifications() {
                Push.create("Hello Giorgio!", {
                    body: "Welcome to the Dashboard.",
                    timeout: 5000,
                    icon: iconPath,
                    tag: 'notification-1' // Unique tag
                });

                setTimeout(() => {
                    Push.create("Hello Giorgio2!", {
                        body: "Welcome to the Dashboard.",
                        timeout: 5000,
                        icon: iconPath,
                        tag: 'notification-2' // Unique tag
                    });
                }, 100); // Slight delay
            }
        });
    </script>
</div>
