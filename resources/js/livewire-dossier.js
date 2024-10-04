document.addEventListener('DOMContentLoaded', function() {
    // Initialize Copy Button
    const copyButton = document.getElementById('copyButton');
    if (copyButton) {
        copyButton.addEventListener('click', function() {
            const data = this.dataset.folder;
            navigator.clipboard.writeText(data).then(() => {
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                alert('Référence copiée!');
            }).catch(err => {
                console.error('Failed to copy data to clipboard:', err);
            });
        });
    }

    // Initialize Delete Buttons
    function initializeDeleteButtons() {
        document.querySelectorAll('.delete_photo').forEach(button => {
            button.addEventListener('click', function() {
                const link = this.dataset.val;
                fetch('/delete_file', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ link })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Successfully deleted:', data);
                    Livewire.emit('refreshDossier');
                })
                .catch(error => {
                    console.error('Error deleting file:', error);
                });
            });
        });
    }

    initializeDeleteButtons();

    // Re-initialize after Livewire updates
    Livewire.hook('message.processed', (message, component) => {
        initializeDeleteButtons();
    });

    // Initialize Dropzones and PDF Modals as needed
    Livewire.on('initializeDropzones', (data) => {
        initializeDropzones(data.forms_configs);
    });

    Livewire.on('setTab', (data) => {
        initializeDropzones(data.forms_configs);
    });
});

// Define other initialization functions like initializeDropzones and initializePdfModals here
function initializeDropzones(configs) {
    // Your Dropzone initialization logic
}

function initializePdfModals() {
    // Your PDF modal initialization logic
}
