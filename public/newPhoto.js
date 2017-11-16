window.onload = () =>{
  document.getElementById("photoItem").src = `https://picsum.photos/800/500/?image=${Math.round(Math.random()* 49)}`
};


const newImage = function() {
  this.src = `https://picsum.photos/800/500/?image=${Math.round(Math.random()* 49)}`
  document.getElementById("upbtn").className = "fa fa-thumbs-o-up";
  document.getElementById("downbtn").className = "fa fa-thumbs-o-down";
}

const upVote = function() {  //arrow function for "this" will not work
  this.className = "fa fa-thumbs-up";
  const PicId = document.getElementById("photoItem").src.match(/=(\d+)/)[1];
  fetch(`/upVoteCount`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'}, //tells the route the body is in json so we can get params from it.
    body: JSON.stringify({chosenPhoto: PicId})
  }).then((dataStream) => dataStream.json())
    .then((message) => {
      console.log('ajax response', message);
    document.getElementById("voteTally").innerHTML = `total likes: ${message.upvote} total dislikes: ${message.downvote}`;
  }).catch((error) =>{
    console.log(error);
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
