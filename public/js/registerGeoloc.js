let autocomplete;
function initAutocomplete() {
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("autocomplete"),
    { types: ["(regions)"], fields: ["place_id", "geometry", "name"] }
  );
  autocompleteVoyagesAccomplis = new google.maps.places.Autocomplete(
    document.getElementById("autocompleteVoyagesAccomplis"),
    { types: ["(regions)"], fields: ["place_id", "geometry", "name"] }
  );

  autocomplete.addListener("place_changed", onPlaceChanged);

  autocompleteVoyagesAccomplis.addListener(
    "place_changed",
    onPlaceChangedVoyagesAccomplis
  );
}

var place;

function onPlaceChanged() {
  document.getElementById("map").classList.remove("mapHidden");
  place = autocomplete.getPlace();
  console.log(place);
  let map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 23.8862915, lng: 0 },
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
  removeFormButton.innerText = "Delete this place";

  placeFormLi.append(removeFormButton);

  removeFormButton.addEventListener("click", (e) => {
    e.preventDefault();
    // remove the li for the tag form
    placeFormLi.remove();
  });
};

var index;

const addFormToCollection = (e) => {
  console.log(place.name);
  const collectionHolder = document.querySelector(
    "." + e.currentTarget.dataset.collectionHolderClass
  );

  const item = document.createElement("li");

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

document.querySelectorAll(".add_item_link").forEach((btn) =>
  btn.addEventListener("click", function (e) {
    console.log("cliqué");
    console.log(place);
    addFormToCollection(e);
    document.getElementById(
      "registration_form_projetsVoyages_" + index + "_name"
    ).value = place.name;
    document.getElementById(
      "registration_form_projetsVoyages_" + index + "_placeId"
    ).value = place.place_id;
    let node = document.createElement("li");
    let textnode = document.createTextNode(place.name);
    node.appendChild(textnode);
    document
      .getElementById("registration_form_projetsVoyages_" + index)
      .appendChild(node);
  })
);

document.querySelectorAll(".add_item_linkVoyagesAccomplis").forEach((btn) =>
  btn.addEventListener("click", function (e) {
    console.log("cliqué");
    addFormToCollection(e);
    // console.log("registration_form_voyagesAccomplis_" + index + "_name");
    // console.log(
    //   document.getElementById(
    //     "registration_form_voyagesAccomplis_" + index + "_name"
    //   )
    // );
    document.getElementById(
      "registration_form_voyagesAccomplis_" + index + "_name"
    ).value = placeVoyagesAccomplis.name;
    document.getElementById(
      "registration_form_voyagesAccomplis_" + index + "_placeId"
    ).value = placeVoyagesAccomplis.place_id;

    let node = document.createElement("li");
    let textnode = document.createTextNode(placeVoyagesAccomplis.name);
    node.appendChild(textnode);
    document
      .getElementById("registration_form_voyagesAccomplis_" + index)
      .appendChild(node);
  })
);
