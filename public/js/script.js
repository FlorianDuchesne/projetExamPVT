
// Tentative de factorisation (échec total pour l'instant… il faut que je révise les fonctions et les portées !…)
// C'est bon j'ai trouvé

console.log("test");
var prevImage = false;
var nextImage = false;

// Open the Modal
let posts = document.querySelectorAll(
  "figure.post__wrapperPictures--many > img, figure.post__wrapperPictures > img"
);
console.log(posts);

// je rassemble toutes les figures dans une constante
for (var post of posts) {
  // pour CHAQUE figure de cette constante…
  post.addEventListener("click", function () {
    console.log("test clic");
    // au clic de la figure
    lightbox(this);
    // je déclenche la fonction lightbox
  });
}

function lightbox(elem) {
  var currentImage = elem;
  // la figure ayant déclenché la fonction, et qui est l'objet de la fonction, est stockée dans la var "currentImage"
  var src = currentImage.src;
  var legende = currentImage.nextElementSibling.firstChild.data;

  // l'élément ( = src) de l'élément qui est le premier enfant de ma figure ( = img) est stocké dans la var "src"
  document.getElementById("lightbox").style.opacity = 1;
  document.getElementById("lightbox").style.zIndex = 100;
  document.getElementById("lightbox").style.top = 0;
  console.log("lightbox on");

  // j' "allume" la lightbox
  document.getElementById(
    "lightbox"
  ).children[2].innerHTML = `<img src="${src}"/>`;
  document.getElementById(
    "lightbox"
  ).children[2].innerHTML += `<figcaption>"${legende}"</figcaption>`;

  // Dans le troisième enfant de #lightbox, je rajoute ma var "src" à l'intérieur d'une balise img rajoutée dans l'HTML, sur js
  document.querySelector("#croix").addEventListener("click", function () {
    closeLightbox(this);
  });
  // Quand je clique sur la croix, j'ouvre la fonction de fermeture de la lightbox

  document.querySelector("#left").addEventListener("click", function () {
    clicGauche();
  });
  // lorsque je clique sur la flèche de gauche, j'active la fonction au clic

  // Ci-dessous, même chose que ci-dessus mais dans l'autre sens…
  document.querySelector("#right").addEventListener("click", function () {
    clicDroit();
  });

  window.addEventListener("keydown", (e) => {
    switch (e.key) {
      case "ArrowLeft":
        console.log("arrowleft");
        clicGauche();
        break;
      case "ArrowRight":
        console.log("arrowright");
        clicDroit();
        break;
      case "Escape":
        console.log("Escape");
        closeLightbox();
        break;  
    }
  });

  function clicGauche() {
    document.getElementById("lightbox").children[2].innerHTML = ``;
    // j'efface le inner html de la lightbox
    function prevImage() {
      prevImage = currentImage.previousElementSibling;
      if (prevImage === null) {
        // Si l'image précédente n'existe pas
        prevImage =
          currentImage.parentElement.lastElementChild.previousElementSibling;
        // je définis la var prevImage comme le premier élément du parent de la currentImage
      }
      if (prevImage.localName === "figcaption") {
        prevImage = prevImage.previousElementSibling;
      }
      return prevImage;
    }
    prevImage();
    while (prevImage.style.display === "none") {
      // Tant que l'image précédente n'est pas en display,
      currentImage = prevImage;
      // je définis la currentImage à partir de la var prevImage
      prevImage();
    }
    src = prevImage.src;
    legende = prevImage.nextElementSibling.firstChild.data;
    // la variable src est définie comme l'élément src enfant de ma variable prevImage
    document.getElementById(
      "lightbox"
    ).children[2].innerHTML = `<img src="${src}"/>`;
    document.getElementById(
      "lightbox"
    ).children[2].innerHTML += `<figcaption>"${legende}"</figcaption>`;
    // le troisème enfant de #lightbox se voit affecter d'une balise img chargée de ma variable src
    currentImage = prevImage;
    // la currentImage se définit à partir de prevImage
    return src, legende, currentImage, prevImage;
  }

  function clicDroit() {
    document.getElementById("lightbox").children[2].innerHTML = ``;
    function nextImage() {
      nextImage = currentImage.nextElementSibling;
      if (nextImage.localName === "figcaption") {
        nextImage = nextImage.nextElementSibling;
      }
      if (nextImage === null) {
        nextImage = currentImage.parentElement.firstElementChild;
      }
      return nextImage;
    }
    nextImage();
    while (nextImage.style.display === "none") {
      currentImage = nextImage;
      nextImage();
    }
    src = nextImage.src;
    legende = nextImage.nextElementSibling.firstChild.data;
    document.getElementById(
      "lightbox"
    ).children[2].innerHTML = `<img src="${src}"/>`;
    document.getElementById(
      "lightbox"
    ).children[2].innerHTML += `<figcaption>"${legende}"</figcaption>`;
    currentImage = nextImage;
    return src, legende, currentImage, nextImage;
  }
}

function closeLightbox(elem) {
  document.getElementById("lightbox").style.opacity = 0;
  document.getElementById("lightbox").style.zIndex = -1;
  // je "ferme" la lightbox
}

function filePreview(input) {
  if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        console.log("test_filePreview");
        $('.vich-image > a').remove();
        $('.vich-image').after('<img class="preview" src="'+e.target.result+'" width="450" height="300"/>');
        $('.vich-image').after('<p class="textPreview">Pour information, l\'image d\'avatar choisie sera arrondie, et si elle n\'est pas carrée, elle sera rognée.</p>');
          }
      reader.readAsDataURL(input.files[0]);
  }
}

$("#registration_form_imageFile_file").change(function () {
  filePreview(this);
});


