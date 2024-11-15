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
                // Push.create("Hello {{auth()->user()->name}}!", {
                //     body: "Bienvenue sur le CRM ATLAS.",
                //     timeout: 5000,
                //     icon: iconPath,
                //     tag: 'notification-1',
                //     onClick: function () {
                //         // Open custom link when notification is clicked
                //         window.open('https://crm.genius-market.fr', '_blank');
                //         // Close the notification
                //         this.close();
                //     }
                // });

               
            }
        });
    </script>
</div>
