let autocomplete;
function initAutocomplete() {
  // initialisation du service Place Autocomplete de l'API dans #autocomplete
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("autocomplete"),
    // type de lieux attendus par l'autocomplete, types de champs à retourner
    { types: ["(regions)"], fields: ["place_id", "geometry", "name"] }
  );
  autocompleteVoyagesAccomplis = new google.maps.places.Autocomplete(
    document.getElementById("autocompleteVoyagesAccomplis"),
    { types: ["(regions)"], fields: ["place_id", "geometry", "name"] }
  );
  // "place_changed" est un type d'événement qui provient de l'API
  autocomplete.addListener("place_changed", onPlaceChanged);

  autocompleteVoyagesAccomplis.addListener(
    "place_changed",
    onPlaceChangedVoyagesAccomplis
  );
}

var place;

function onPlaceChanged() {
  document.getElementById("map").classList.remove("mapHidden");
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

var placeVoyagesAccomplis;

function onPlaceChangedVoyagesAccomplis() {
  console.log("il se passe un truc");
  document.getElementById("mapVoyagesAccomplis").classList.remove("mapHidden");
  placeVoyagesAccomplis = autocompleteVoyagesAccomplis.getPlace();
  console.log(placeVoyagesAccomplis);
  let map = new google.maps.Map(
    document.getElementById("mapVoyagesAccomplis"),
    {
      center: { lat: 23.8862915, lng: 0 },
      zoom: 2,
    }
  );

  var service = new google.maps.places.PlacesService(map);
  service.getDetails(
    {
      placeId: placeVoyagesAccomplis.place_id,
    },
    function (placeVoyagesAccomplis, status) {
      if (status == google.maps.places.PlacesServiceStatus.OK) {
        var marker = new google.maps.Marker({
          map: map,
          place: {
            placeId: placeVoyagesAccomplis.place_id,
            location: placeVoyagesAccomplis.geometry.location,
          },
        });
      }
    }
  );
  return placeVoyagesAccomplis;
}

///////////////////// ajout marqueurs carte inscription //////////////
const addPlaceFormDeleteLink = (placeFormLi) => {
  const removeFormButton = document.createElement("button");
  removeFormButton.classList;
  removeFormButton.innerHTML = "<i class='fas fa-trash-alt'></i>";

  placeFormLi.append(removeFormButton);

  removeFormButton.addEventListener("click", (e) => {
    e.preventDefault();
    // remove the li for the tag form
    placeFormLi.remove();
  });
};

var index;

const addFormToCollection = (e) => {
  // console.log(place.name);
  const collectionHolder = document.querySelector(
    "." + e.currentTarget.dataset.collectionHolderClass
  );

  const item = document.createElement("li");
  // item.className = "place list-group-item";

  item.innerHTML = collectionHolder.dataset.prototype.replace(
    /__name__/g,
    collectionHolder.dataset.index
  );
  // console.log(addFormToCollection);

  collectionHolder.appendChild(item);

  addPlaceFormDeleteLink(item);

  index = collectionHolder.dataset.index;

  collectionHolder.dataset.index++;

  return index;
};
// Lorsqu'un utilisateur valide le lieu sélectionné, je l'ajoute à une collection de mon formulaire
// en donnant les valeurs de son nom et de son place_id aux champs correspondants
document.querySelectorAll(".add_item_link").forEach((btn) =>
  btn.addEventListener("click", function (e) {
    addFormToCollection(e);
    document.getElementById(
      "edit_user_projetsVoyages_" + index + "_name"
    ).value = place.name;
    document.getElementById(
      "edit_user_projetsVoyages_" + index + "_placeId"
    ).value = place.place_id;
    // J'indique à l'utilisateur que le lieu est bien inscrit au formulaire en ajoutant
    // son nom à une liste à puces
    let node = document.createElement("li");
    node.className = "place list-group-item";
    let textnode = document.createTextNode(place.name);
    node.appendChild(textnode);
    document
      .getElementById("edit_user_projetsVoyages_" + index)
      .appendChild(node);
  })
);

document.querySelectorAll(".add_item_linkVoyagesAccomplis").forEach((btn) =>
  btn.addEventListener("click", function (e) {
    console.log("cliqué");
    addFormToCollection(e);
    // console.log("edit_user_voyagesAccomplis_" + index + "_name");
    // console.log(
    //   document.getElementById(
    //     "edit_user_voyagesAccomplis_" + index + "_name"
    //   )
    // );
    document.getElementById(
      "edit_user_voyagesAccomplis_" + index + "_name"
    ).value = placeVoyagesAccomplis.name;
    document.getElementById(
      "edit_user_voyagesAccomplis_" + index + "_placeId"
    ).value = placeVoyagesAccomplis.place_id;

    let node = document.createElement("li");
    node.className = "place list-group-item";
    let textnode = document.createTextNode(placeVoyagesAccomplis.name);
    node.appendChild(textnode);
    document
      .getElementById("edit_user_voyagesAccomplis_" + index)
      .appendChild(node);
  })
);
