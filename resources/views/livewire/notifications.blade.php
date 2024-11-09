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
                    tag: 'notification-1',
                    onClick: function () {
                        // Open custom link when notification is clicked
                        window.open('https://example.com/your-custom-link-1', '_blank');
                        // Close the notification
                        this.close();
                    }
                });

                setTimeout(() => {
                    Push.create("Hello Giorgio2!", {
                        body: "Check out our new features.",
                        timeout: 5000,
                        icon: iconPath,
                        tag: 'notification-2',
                        onClick: function () {
                            // Open another custom link when notification is clicked
                            window.open('https://example.com/your-custom-link-2', '_blank');
                            // Close the notification
                            this.close();
                        }
                    });
                }, 100); // Slight delay
            }
        });
    </script>
</div>
