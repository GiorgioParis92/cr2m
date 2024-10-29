<div wire:poll.1s="fetchPositions">
    <div id="map" wire:ignore style="height: 100vh; width: 100%;"></div>
  
    <!-- Include Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
    <script>
  document.addEventListener('livewire:load', function () {
  let mapInitialized = false;
  let map;
  let polylines = {}; // Store polylines by user_id
  let lastPositionsByUser = {};
  let userColors = {}; // Store colors for each user

  function initMap() {
    map = L.map('map').setView([48.829833, 2.245062], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    mapInitialized = true;
  }

  function updateMap(event) {
    const positionsByUser = event.detail.positionsByUser;
    console.log('Positions by User:', positionsByUser); // Debugging

    if (!mapInitialized) {
      initMap();
    }

    // Loop through each user
    for (const userId in positionsByUser) {
      const userPositions = positionsByUser[userId];
      const newPoints = userPositions.map(pos => [parseFloat(pos.lat), parseFloat(pos.lng)]);

      console.log('Processing user:', userId); // Debugging
      console.log('New points:', newPoints);    // Debugging

      if (newPoints.length === 0) {
        continue; // No points to display for this user
      }

      // Check if the positions have changed
      if (JSON.stringify(newPoints) !== JSON.stringify(lastPositionsByUser[userId])) {
        if (polylines[userId]) {
          // Update existing polyline
          polylines[userId].setLatLngs(newPoints);
        } else {
          // Create a new polyline with a unique color
          const color = getUserColor(userId);

          polylines[userId] = L.polyline(newPoints, {
            color: color,
            weight: userId === '1' ? 3 : 6, // Different weights
            opacity: 0.7,
            dashArray: userId === '1' ? '5,5' : null, // Different styles
          }).addTo(map);

          // Optionally add a popup for the user
          polylines[userId].bindPopup('User ID: ' + userId);

          // Add start and end markers
          const startPoint = newPoints[0];
          const endPoint = newPoints[newPoints.length - 1];

          L.marker(startPoint, { title: `User ${userId} Start` }).addTo(map);
          L.marker(endPoint, { title: `User ${userId} End` }).addTo(map);
        }

        // Update the last positions
        lastPositionsByUser[userId] = newPoints;
      }
    }

    // Adjust the map view to fit all polylines
    if (Object.keys(polylines).length > 0) {
      const group = new L.featureGroup(Object.values(polylines));
      map.fitBounds(group.getBounds());
    }
  }

  // Helper function to generate a consistent color for each user
  function getUserColor(userId) {
    const userColorsMapping = {
      '1': '#FF0000',  // Red for user 1
      '76': '#0000FF', // Blue for user 76
    };
    return userColorsMapping[userId] || getRandomColor();
  }

  // Helper function to generate a random color
  function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
  }

  // Listen to the browser event dispatched by Livewire
  window.addEventListener('positions-updated', updateMap);
});

    </script>
  </div>
  