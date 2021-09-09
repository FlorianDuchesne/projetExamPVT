$(document).ready(function () {
  // let markers = document.querySelector("#markers");
  // console.log(markers);
  // let marker = markers.querySelectorAll(".marker");
  // console.log(marker);
  // let places = [];
  // marker.forEach((element) => places.push(element.children[1].innerText));
  // console.log(places);
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

  let markersPlaces = [];
  let markersNames = [];
  let markersStatuts = [];

  $(document).ready(function () {
    console.log(document.URL);
    let adress = document.URL;
    console.log(adress.length);
    let userId = adress.slice(27, adress.length);
    console.log(userId);
    let url = "/searchPlaceId/" + userId;
    console.log(url);
    $.ajax({
      url: url,
      type: "POST",
    }).done(function (response) {
      console.log(response);
      markersPlaces.push(response.placesId);
      markersNames.push(response.placesNames);
      markersStatuts.push(response.placesStatut);
      // console.log("markersPlaces");
      // console.log(markersPlaces[0]);
      // console.log(markersStatuts[0][0]);
      // console.log(markersPlaces.length);
      var i = 0;

      var service = new google.maps.places.PlacesService(mapProfile);
      // pour chaque élément, incrémenter
      markersPlaces[0].forEach((element) =>
        service.getDetails(
          {
            placeId: element,
          },
          function (result, status) {
            console.log(markersStatuts[0][i]);
            if (markersStatuts[0][i] == true) {
              var icon = "/img/redPin.png";
            } else {
              var icon = "/img/bluePin.png";
            }
            console.log(markersNames[0][i]);
            i++;
            console.log("element");
            console.log(element);
            var marker = new google.maps.Marker({
              icon: icon,
              map: mapProfile,
              place: {
                placeId: element,
                location: result.geometry.location,
              },
            });
          }
        )
      );
    });
  });
}
