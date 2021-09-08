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
    $.ajax({
      url: "/searchPlaceId",
      type: "POST",
    }).done(function (response) {
      // console.log(response);
      markersPlaces.push(response.placesId);
      markersNames.push(response.placesNames);
      markersStatuts.push(response.placesStatut);
      console.log("markersPlaces");
      console.log(markersPlaces[0]);
      // console.log(markersStatuts[0]);
      // console.log(markersPlaces.length);

      var service = new google.maps.places.PlacesService(mapProfile);
      // pour chaque élément, incrémenter
      markersPlaces[0].forEach((element) =>
        service.getDetails(
          {
            placeId: element,
          },
          function (result, status) {
            console.log("element");
            console.log(element);
            var marker = new google.maps.Marker({
              map: mapProfile,
              place: {
                placeId: element,
                location: result.geometry.location,
              },
            });
          }
        )
      );

      // console.log(markersNames[0]);
      // console.log(markersStatuts[0]);
      // return markersPlaces, markersNames, markersStatuts;
    });
    // return markersPlaces, markersNames, markersStatuts;
  });

  // console.log(markersPlaces);

  // console.log(markers);
  // markersPlaces.forEach((element) => console.log(element));

  // console.log(markers.indexOf("Fr0ancfort--le-Main"));
  // markers.forEach(function (item, index, array) {
  //   console.log(item, index);
  // });
  // markers.forEach((element) => console.log(element));

  // The marker, positioned at Uluru
}

// let markersPlaces;
// let markersNames;
// let markersStatuts;
// let markersPlaces = [];
// let markersNames = [];
// let markersStatuts = [];

// $(document).ready(function () {
//   $.ajax({
//     url: "/searchPlaceId",
//     type: "POST",
//   }).done(function (response) {
//     // console.log(response);
//     markersPlaces.push(response.placesId);
//     markersNames.push(response.placesNames);
//     markersStatuts.push(response.placesStatut);
//     console.log(markersPlaces);
//     console.log(markersPlaces.length);

//     // console.log(markersNames[0]);
//     // console.log(markersStatuts[0]);
//     return markersPlaces, markersNames, markersStatuts;
//   });
//   return markersPlaces, markersNames, markersStatuts;
// });
// console.log(markersPlaces.length);
// console.log(markersNames[0]);
// console.log(markersStatuts[0]);

// });
