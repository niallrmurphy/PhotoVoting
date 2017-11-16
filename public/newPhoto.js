const newImage = function() {
  console.log('you clicked it');
  this.src = `https://picsum.photos/800/500/?image=${Math.round(Math.random()* 49)}`
  document.getElementById("upbtn").className = "fa fa-thumbs-o-up";
  document.getElementById("downbtn").className = "fa fa-thumbs-o-down";
}

const upVote = function() {  //arrow function for "this" will not work
  this.className = "fa fa-thumbs-up";
  PicId = document.getElementById("photoItem").src.match(/=(\d+)/)[1];
  fetch(`/upVoteCount`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'}, //tells the route the body is in json so we can get params from it.
    body: JSON.stringify({"chosenPhoto": PicId})
  })
}

const downVote = function() {
  this.className = "fa fa-thumbs-down"

}

const image = document.getElementById("photoItem");
const upButton = document.getElementById("upbtn")
const downButton = document.getElementById("downbtn")

/*event listeners for image*/
image.addEventListener("click", newImage);
//image.addEventListener("mouseover", )

upButton.addEventListener("click", upVote);
downButton.addEventListener("click", downVote);
