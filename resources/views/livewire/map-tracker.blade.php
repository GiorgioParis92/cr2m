<div wire:poll.1s="fetchPositions">
    <div id="map" wire:ignore style="height: 100vh; width: 100%;"></div>
  
    <!-- Include Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
    <script>
      document.addEventListener('livewire:load', function () {
        let mapInitialized = false;
        let map;
        let polylines = {}; // Store polylines by userId
        let lastPositionsByUser = {};
        let mapViewAdjusted = false; // Flag to prevent repeated fitBounds calls
  
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
  
          let newPolylinesAdded = false;
  
          // Loop through each user
          for (const userId in positionsByUser) {
            const userData = positionsByUser[userId];
            const userName = userData.userName;
            const userPositions = userData.positions;
            const newPoints = userPositions.map(pos => [parseFloat(pos.lat), parseFloat(pos.lng)]);
            const colorCode = userData.colorCode; // Get color code from server
  
            console.log('Processing user:', userName); // Debugging
            console.log('New points:', newPoints);    // Debugging
  
            if (newPoints.length === 0) {
              continue; // No points to display for this user
            }
  
            // Use userId as the key internally
            const userKey = userId;
  
            // Check if the positions have changed
            if (JSON.stringify(newPoints) !== JSON.stringify(lastPositionsByUser[userKey])) {
              if (polylines[userKey]) {
                // Update existing polyline
                polylines[userKey].setLatLngs(newPoints);
              } else {
                // Create a new polyline with the color from the server
                const color = colorCode;
  
                polylines[userKey] = L.polyline(newPoints, {
                  color: color,
                  weight: 3,
                  opacity: 0.7,
                }).addTo(map);
  
                // Add a popup with the user's name
                polylines[userKey].bindPopup('User: ' + userName);
  
                // Add start and end markers
                const startPoint = newPoints[0];
                const endPoint = newPoints[newPoints.length - 1];
  
                L.marker(startPoint, { title: `${userName} Start` }).addTo(map);
                L.marker(endPoint, { title: `${userName} End` }).addTo(map);
  
                newPolylinesAdded = true;
              }
  
              // Update the last positions
              lastPositionsByUser[userKey] = newPoints;
            }
          }
  
          // Remove polylines for users that no longer have positions
          for (const userKey in polylines) {
            if (!positionsByUser[userKey]) {
              map.removeLayer(polylines[userKey]);
              delete polylines[userKey];
              delete lastPositionsByUser[userKey];
            }
          }
  
          // Adjust map view to include all polylines, but only once (on initial load)
          if (!mapViewAdjusted && Object.keys(polylines).length > 0) {
            const group = new L.featureGroup(Object.values(polylines));
            map.fitBounds(group.getBounds());
            mapViewAdjusted = true; // Prevent further adjustments
          }
        }
  
        // Listen to the browser event dispatched by Livewire
        window.addEventListener('positions-updated', updateMap);
      });
    </script>
  </div>
  