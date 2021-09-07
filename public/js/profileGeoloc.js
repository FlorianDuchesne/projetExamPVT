/////////// API GEOLOC ////////////
$(document).ready(function () {
  let markers = document.querySelector("#markers");
  console.log(markers);
  let marker = markers.querySelectorAll(".marker");
  console.log(marker);
  let places = [];
  marker.forEach((element) => places.push(element.children[1].innerText));
  console.log(places);
});
function initMap() {
  // The map, centered at Uluru
  const mapProfile = new google.maps.Map(
    document.getElementById("mapProfile"),
    {
      zoom: 2,
      center: { lat: 23.8862915, lng: 0 },
    }
  );
  // The marker, positioned at Uluru
}
