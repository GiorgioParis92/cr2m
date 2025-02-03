<div>
    <!-- Typically in your <head>: -->
<script src="https://unpkg.com/alpinejs" defer></script>

<div
    x-data="{
        connectionType: 'unknown',
        
        init() {
            // 1. Set up a function to update connectionType
            const updateConnectionType = () => {
                // Fallback if API not supported
                if (!navigator.connection || !navigator.connection.effectiveType) {
                    this.connectionType = 'unsupported';
                } else {
                    this.connectionType = navigator.connection.effectiveType;
                    alert('ok')
                }
                
                // Push changes up to Livewire
                $wire.set('connectionType', this.connectionType);
            };
            
            // 2. Run once on init
            updateConnectionType();
            
            // 3. Listen for changes (e.g., user switches from Wi-Fi to 4G)
            if (navigator.connection) {
                navigator.connection.addEventListener('change', updateConnectionType);
            }
            
            // 4. Also handle offline/online if needed
            window.addEventListener('online', () => {
                // Possibly handle going online here
                // $wire.set('connectionType', 'online');
            });
            
            window.addEventListener('offline', () => {
                // Possibly handle going offline here
                // $wire.set('connectionType', 'offline');
            });
        }
    }"
    class="p-4 border"
>
    <h2 class="font-bold mb-2">Connection Status</h2>
    <p>Network type (effective): <strong x-text="connectionType"></strong></p>
    {{$connectionType}}
    <!-- 
      You could conditionally show warnings or messages:
      <template x-if="connectionType === '2g' || connectionType === 'slow-2g'">
        <p>Your connection is very slow!</p>
      </template>
    -->


<script>
    console.log('navigator.connection:', navigator.connection);
console.log('navigator.connection.effectiveType:', navigator.connection?.effectiveType);

</script>

</div>
</div>
