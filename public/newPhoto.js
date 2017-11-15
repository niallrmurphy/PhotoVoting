const newImage = () => {
  console.log('you clicked it');
  document.getElementById("photoItem").src = `https://picsum.photos/800/500/?image=${Math.round(Math.random()* 49)}`

}

const image = document.getElementById("photoItem");

image.addEventListener("click", newImage, true);
