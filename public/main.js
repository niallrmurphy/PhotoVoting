
/**
 * Sets src of image to a new random image and
 * resets the button visuals (count and outline).
 * @return {undefined}
 */
function newPage() {
  this.src = randomImage();
  const up = document.getElementById('upbtn');
  const down = document.getElementById('downbtn');
  up.classList.add('fa-thumbs-o-up');
  up.classList.remove('fa-thumbs-up');
  down.classList.add('fa-thumbs-o-down');
  down.classList.remove('fa-thumbs-down');
  up.innerHTML = '  ';
  down.innerHTML = '  ';
}

/**
 * Determine whether we are adding a vote or removing it
 * @param  {object} element The html button logging the vote
 * @return {boolean} true if vote is being added to count, false if being removed
 */
function addingOrRemoving(element) {
  if(element.classList.contains('fa-thumbs-o-up') || element.classList.contains('fa-thumbs-o-down')){
    return true;
  }
  return false;
}

/**
 * Indicates vote on the DOM, calls fetch to log vote
 * and get back total votes counts, updates DOM accordingly.
 * @return {undefined}
 */
function vote() { // arrow function for 'this' will not work
  const PicId = document.getElementById('photoItem').src.match(/=(\d+)/)[1];
  const thumb = /(.+)btn/.exec(this.id)[1]; // thumb up or thumb down?
  const increasingVote = addingOrRemoving(this);

  if (increasingVote) {
    this.classList.add(`fa-thumbs-${thumb}`);
    this.classList.remove(`fa-thumbs-o-${thumb}`);
  } else {
    this.classList.add(`fa-thumbs-o-${thumb}`);
    this.classList.remove(`fa-thumbs-${thumb}`);
  }

  fetch('/countVote', {
    method: 'POST',
    // tells the route the body is in json so we can get params from it.
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chosenPhoto: PicId, thumb, increasingVote }),
  }).then(dataStream => dataStream.json())
    .then((message) => {
      document.getElementById('upbtn').innerHTML = message.upvote;
      document.getElementById('downbtn').innerHTML = message.downvote;
    }).catch((error) => {
      console.log(error);
    });
}

/**
 * Sets src of the image to a new random image
 * @return {undefined}
 */
window.onload = () => {
  document.getElementById('photoItem').src = randomImage();
};

const image = document.getElementById('photoItem');
const upButton = document.getElementById('upbtn');
const downButton = document.getElementById('downbtn');

/* event listeners for image */
image.addEventListener('click', newImage);

/* event listeners for buttons */
upButton.addEventListener('click', vote);
downButton.addEventListener('click', vote);
