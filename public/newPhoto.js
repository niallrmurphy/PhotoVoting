function randomImage() {
  return `https://picsum.photos/800/500/?image=${Math.round(Math.random() * 49)}`;
}

window.onload = () => {
  document.getElementById('photoItem').src = randomImage();
};

function newImage() {
  this.src = randomImage();
  const up = document.getElementById('upbtn');
  const down = document.getElementById('downbtn')
  up.classList.add('fa-thumbs-o-up');
  down.classList.add('fa-thumbs-o-down');
  up.classList.remove('fa-thumbs-up');
  down.classList.remove('fa-thumbs-down');
  up.innerHTML = ' ';
  down.innerHTML = ' ';
}

function vote() { // arrow function for 'this' will not work
  const PicId = document.getElementById('photoItem').src.match(/=(\d+)/)[1];
  let thumb = '';
  if(this.id === 'upbtn'){
    this.classList.add('fa-thumbs-up');
    this.classList.remove('fa-thumbs-o-up');
    thumb = 'up';
  }
  else { //must be down butto
    this.classList.add('fa-thumbs-down');
    this.classList.remove('fa-thumbs-o-down');
    thumb = 'down'
  }
  fetch('/countVote', {
    method: 'POST',
    // tells the route the body is in json so we can get params from it.
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chosenPhoto: PicId, thumb }),
  }).then(dataStream => dataStream.json())
    .then((message) => {
      document.getElementById('upbtn').innerHTML = message.upvote;
      document.getElementById('downbtn').innerHTML = message.downvote;
    }).catch((error) => {
      console.log(error);
    });
}

const image = document.getElementById('photoItem');
const upButton = document.getElementById('upbtn');
const downButton = document.getElementById('downbtn');

/* event listeners for image */
image.addEventListener('click', newImage);

/* event listeners for buttons */
upButton.addEventListener('click', vote);
downButton.addEventListener('click', vote);
