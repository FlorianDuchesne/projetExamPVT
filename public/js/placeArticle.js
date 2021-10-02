let autocomplete;
function placeArticle() {
  // initialisation du service Place Autocomplete de l'API dans #autocomplete
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("autocomplete"),
    // type de lieux attendus par l'autocomplete, types de champs à retourner
    { types: ["(regions)"], fields: ["place_id", "geometry", "name"] }
  );
  // "place_changed" est un type d'événement qui provient de l'API
  autocomplete.addListener("place_changed", onPlaceChanged);
}

var place;

function onPlaceChanged() {
  // getPlace() est une méthode de l'API qui retourne les détails demandés du lieu.
  place = autocomplete.getPlace();
  // On initialise la carte grâce à l'API dans #map
  let map = new google.maps.Map(document.getElementById("map"), {
    // Centre de la carte
    center: { lat: 23.8862915, lng: 0 },
    // Echelle de la carte
    zoom: 2,
  });

  var service = new google.maps.places.PlacesService(map);
  service.getDetails(
    {
      placeId: place.place_id,
    },
    function (place, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        var marker = new google.maps.Marker({
          map: map,
          place: {
            placeId: place.place_id,
            location: place.geometry.location,
          },
        });
      }
    }
  );
  return place;
}

var lieuHidden = document.getElementById("#lieuHidden");

if (lieuHidden) {
  document.querySelector("#article_placeName").value = lieuHidden.value;
  document.querySelector("#lieu").innerHTML += lieuHidden.value +=
    " <button class='deleteLieu'><i class='fas fa-trash-alt'></i></button>";
}

var deleteLieu = document.querySelector(".deleteLieu");

function deleteLieu() {
  console.log("ça marche");
  document.querySelector("#lieu").innerHTML = "";
  document.querySelector("#article_placeName").value = null;
  console.log(document.querySelector("#article_placeName"));
  document.querySelector("#article_placeId").value = null;
  console.log(document.querySelector("#article_placeId"));
}

// Lorsqu'un utilisateur valide le lieu sélectionné, je l'ajoute à une collection de mon formulaire
// en donnant les valeurs de son nom et de son place_id aux champs correspondants
document
  .querySelector(".add_item_link")
  .addEventListener("click", function (e) {
    console.log(place.name);
    console.log(place.place_id);
    document.querySelector("#article_placeName").value = place.name;
    console.log(document.querySelector("#article_placeName"));
    document.querySelector("#article_placeId").value = place.place_id;
    console.log(document.querySelector("#article_placeId"));
    document.querySelector("#lieu").innerHTML =
      "Lieu : " +
      place.name +
      " <button class='deleteLieu'><i class='fas fa-trash-alt'></i></button>";
    document.querySelector(".deleteLieu").addEventListener("click", (e) => {
      e.preventDefault();
      document.querySelector("#lieu").innerHTML = "";
      document.querySelector("#article_placeName").value = null;
      console.log(document.querySelector("#article_placeName"));
      document.querySelector("#article_placeId").value = null;
      console.log(document.querySelector("#article_placeId"));
    });

    // document.getElementById(
    //   "registration_form_places_" + index + "_name"
    // ).value = place.name;
    // document.getElementById(
    //   "registration_form_places_" + index + "_placeId"
    // ).value = place.place_id;
    // // J'indique à l'utilisateur que le lieu est bien inscrit au formulaire en ajoutant
    // // son nom à une liste à puces
    // document
    //   .getElementById("registration_form_places_" + index)
    //   .parentNode.before(place.name);
  });

if (deleteLieu) {
  deleteLieu.addEventListener("click", (e) => {
    e.preventDefault();
    deleteLieu();
  });
}
