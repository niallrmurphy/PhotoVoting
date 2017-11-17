const express = require('express');
const bodyParser = require('body-parser');
const db = require('./db/db');

const app = express();

// need this for bodyParser
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// express knows to look for the index.html file in the folder specified.
app.use(express.static('public'));

// express route for POST requests to db
app.post('/countVote', (req, res) => {
  db.addVote(req.body.chosenPhoto, req.body.thumb)
    .then(message => res.json(message))
    .catch(e => console.log(e));
});

app.listen(3000, () => {
  console.log('Listening on port %s...', server.address().port); // string interpolation
});
