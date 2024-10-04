<p><b>Click on the map to add the location</b></p><br/> 
<div id="map" style="height:400px;width:500px;"></div>
<p>

<input name="latitude" id="LatTxt" type="hidden" value="11.21367525852147" /></label>
<input name="longitute" id="LonTxt" type="hidden" value="123.73793119189466" /></label>
</p>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initialize"></script>
<script>
var directionsDisplay,
    directionsService,
    map;

function initialize() {
  var directionsService = new google.maps.DirectionsService();
  directionsDisplay = new google.maps.DirectionsRenderer();
  var chicago = new google.maps.LatLng(41.850033, -87.6500523);
  var mapOptions = { zoom:7, mapTypeId: google.maps.MapTypeId.ROADMAP, center: chicago }
  map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
  directionsDisplay.setMap(map);
}

</script>
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
    var map;
var markersArray = []; // to be used later to clea overlay array
jQuery(document).ready(function() {
    var lat = parseFloat($('#LatTxt').val());
    var lon = parseFloat($('#LonTxt').val());
    var marker;
    var myLatlng = new google.maps.LatLng(lat, lon);
    var myOptions = {
        zoom: 4,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($('#map')[0], myOptions);
    placeMarker(myLatlng);

    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
    });

    //Function to extract longitude

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
            markersArray.push(marker); // to be used later to clea overlay array
        }
    }


    // Removes the overlays from the map, but keeps them in the array

    function clearOverlays() {
        if (markersArray) {
            for (var i = 0, length = markersArray.length; i < length; i++) {
                markersArray[i].setMap(null);
            }
        }
    }
    //Jquery update HTML input boxes

    function updatelonlat() {
        jQuery('#LatTxt').val(marker.getPosition().lat());
        jQuery('#LonTxt').val(marker.getPosition().lng());
    }
    // add event click
    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
        //document.getElementById("#LatTxt").value = event.latLng.lat();
        //down lan lon values  from http://tech.cibul.net/geocode-with-google-maps-api-v3/
        updatelonlat();
    });
});
</script>