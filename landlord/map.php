<p><b>Click on the map to add the location</b></p><br/> 
<div id="map" style="height:400px;width:500px;"></div>
<p>
    <input name="latitude" id="LatTxt" type="hidden" value="11.21367525852147" />
    <input name="longitude" id="LonTxt" type="hidden" value="123.73793119189466" />
</p>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initialize" async defer></script>
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
var map;
var marker;

function initialize() {
    var madridejos = new google.maps.LatLng(11.21367525852147, 123.73793119189466); // Coordinates for Madridejos
    var mapOptions = {
        zoom: 12,
        center: madridejos,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map'), mapOptions);
    placeMarker(madridejos);

    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
        updateLatLonInputs(event.latLng);
    });
}

function placeMarker(location) {
    if (marker) {
        marker.setPosition(location);
    } else {
        marker = new google.maps.Marker({
            position: location,
            draggable: true,
            title: 'Drag me',
            map: map
        });
    }
}

function updateLatLonInputs(location) {
    $('#LatTxt').val(location.lat());
    $('#LonTxt').val(location.lng());
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
