function initMap() {
  // On initialise la carte grâce à l'API dans #mapProfile
  const mapProfile = new google.maps.Map(
    document.getElementById("mapArticles"),
    {
      // Echelle de la carte
      zoom: 2,
      // Centre de la carte
      center: { lat: 23.8862915, lng: 0 },
    }
  );

  let markersPlaces = [];
  let markersNames = [];
  let markersId = [];

  $(document).ready(function () {
    // Je récupère l'id du user dont le profil est consulté
    // let adress = document.URL;
    // let userId = adress.slice(27, adress.length);
    // Je paramètre l'Id dans l'url attaché à la fonction
    // que je souhaite appeler dans ma requête ajax
    // let url = "/searchPlaceId/" + userId;
    $.ajax({
      url: "/searchPlaceIdArticles",
      type: "POST",
    }).done(function (response) {
      markersPlaces.push(response.placesId);
      markersNames.push(response.placesNames);
      markersId.push(response.id);
      var i = 0;

      // J'appelle ensuite un service de l'API qui me permettra
      // d'identifier les détails d'un lieu donné
      var service = new google.maps.places.PlacesService(mapProfile);
      // pour chaque élément du tableau om sont enregistrés les place_id…
      markersPlaces[0].forEach((element) =>
        // Je demande les détails du service instancié plus tôt
        service.getDetails(
          {
            placeId: element,
          },
          // Et je lance une fonction avec en paramètre le résultat de getDetails
          function (result, status) {
            // En fonction du booléen enregistré dans markersStatuts,
            // qui indique s'il s'agit d'un voyage réalisé ou à venir,
            // je définis un icône rouge ou bleu.
            var icon = "/img/redPin.png";

            var infowindow = new google.maps.InfoWindow({
              content:
                "<div id='content'><h6><a href='/indexbyLieu/" +
                markersId[0][i] +
                "'>" +
                markersNames[0][i] +
                "</a><h6></div>",
            });

            i++;
            // Grâce à l'API, je définis un marqueur
            var marker = new google.maps.Marker({
              // auquel j'attribue l'icône défini précédemment
              icon: icon,
              // Et la carte initialisée plus tôt
              map: mapProfile,
              // Enfin je localise le marqueur grâce au place_id
              // et au résultat de la méthode getDetails
              place: {
                placeId: element,
                location: result.geometry.location,
              },
            });
            marker.addListener("click", () => {
              infowindow.open({
                anchor: marker,
                mapProfile,
                shouldFocus: false,
              });
            });
          }
        )
      );
    });
  });
}
