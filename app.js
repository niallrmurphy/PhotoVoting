const express = require('express');
const bodyParser = require('body-parser');
const db = require('./db/db');

const app = express();

// need this for bodyParser
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// express knows to look for the index.html file in the folder specified.
app.use(express.static('public'));

// express route for GET and POST requests to db
app.post('/upVoteCount', (req, res) => {
  db.addUpVote(req.body.chosenPhoto)
    .then(() => db.totalVotes(req.body.chosenPhoto)
      .then(number => res.json(number)))
    .catch(e => console.log(e));
});

const server = app.listen(3000, () => {
  console.log('Listening on port %s...', server.address().port); // string interpolation
});
