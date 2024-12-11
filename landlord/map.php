<p><b>Click on the map to add the location</b></p><br />
<div id="map" style="height: 400px; width: 500px;"></div>
<p>
    <input name="latitude" id="LatTxt" type="hidden" value="11.21367525852147" />
    <input name="longitude" id="LonTxt" type="hidden" value="123.73793119189466" />
</p>

<!-- Add Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Add Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Coordinates for Madridejos
    const madridejosCenter = [11.2663, 123.7202]; 
    let map, marker;

    // Initialize the map
    map = L.map('map').setView(madridejosCenter, 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a draggable marker at the default location
    marker = L.marker(madridejosCenter, { draggable: true }).addTo(map);

    // Update latitude and longitude when marker is dragged
    marker.on('dragend', function (e) {
        const latLng = e.target.getLatLng();
        updateLatLonInputs(latLng);
    });

    // Update marker position and inputs when map is clicked
    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng([lat, lng]); // Move marker to clicked location
        updateLatLonInputs(e.latlng);
    });

    // Function to update hidden latitude and longitude inputs
    function updateLatLonInputs(latLng) {
        document.getElementById('LatTxt').value = latLng.lat;
        document.getElementById('LonTxt').value = latLng.lng;
    }
</script>
