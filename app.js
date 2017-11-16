const express = require('express');
const bodyParser = require('body-parser');
const db = require('./db/db');

const app = express();

app.use(bodyParser.urlencoded({ extended: false })); //need this for bodyParser.
app.use(bodyParser.json());


app.use(express.static('public')); //<- it knows to look for the index.html file

app.post("/upVoteCount", (req, res) =>{ // express routing for get and post requests in express
  //console.log(req);
  console.log(req.body);
  db.addUpVote(req.body.chosenPhoto)
    .then(db.totalVotes(req.body.chosenPhoto))
})

const server = app.listen(3000, () => {
  console.log("Listening on port %s...", server.address().port); //string interpolation
})
