const newImage = () => {
  console.log('you clicked it');
  document.getElementById("photoItem").src = `https://picsum.photos/400/250/?image=${Math.round(Math.random()* 49)}`

}

const upVote = function() {  //arrow function for "this" will not work
  this.className = "fa fa-thumbs-up";
  PicId = document.getElementById("photoItem").src.match(/=(\d+)/)[1];
  fetch(`/upVoteCount`, {
    method: 'POST',
    body: {"chosenPhoto": "PicId"}
  })
}

const downVote = function() {
  this.className = "fa fa-thumbs-down"

}

const image = document.getElementById("photoItem");
const upButton = document.getElementById("upbtn")
const downButton = document.getElementById("downbtn")


image.addEventListener("click", newImage);


upButton.addEventListener("click", upVote);
downButton.addEventListener("click", downVote);
