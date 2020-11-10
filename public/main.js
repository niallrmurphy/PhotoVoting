/**
 * Generates a random number between 0-49 for a random image.
 * @return {string} url of random image
 */
function randomImage() {
  return `https://picsum.photos/800/500/?image=${Math.round(Math.random() * 49)}`;
}

function decideWhichImages(group_oriented, display_size) {
  /**
   * If group_oriented is false:
   * Scan the database to find all images, pick display_size random selections
   * (2 by default).
   * If group_oriented is true:
   * Scan the database to find out, for every image, what group(s)
   * it is in (if any). For example, image 1 in groups [1, 2, 3] and
   * image 2 in groups [15].
   * Build a table of all groups, and a mapping between all groups and images.
   * Randomly select a group (with > display_size members).
   * Randomly select display_size images within that group.
   **/
  // Things we'll need no matter what
  if(typeof display_size === 'undefined') {
    display_size = 2;
  }
  console.log(display_size);
  countImages();
  createPhotoArray();
  if(typeof group_oriented === 'undefined')
  {
    _.sample(total_photo_array, display_size);
  }
  else
  {
    //
  }
}

/**
 * Sets src of image to a new random image and
 * resets the button visuals (count and outline).
 * @return {undefined}
 */
function newImage() {
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
