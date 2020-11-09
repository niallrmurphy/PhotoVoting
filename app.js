const express = require('express');
const bodyParser = require('body-parser');
const db = require('./db/db');


const express_sharp = require('express-sharp')
//import { expressSharp, FsAdapter, HttpAdapter } from 'express-sharp'
const server_path = 'public'

const app = express();

// need this for bodyParser
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// express knows to look for the index.html file in the folder specified.
app.use(express.static(server_path));

// express route for POST requests to db
app.post('/countVote', (req, res) => {
  db.addVote(req.body.chosenPhoto, req.body.thumb, req.body.increasingVote)
  //console.dir(res);
    //.run(message => res.json(message))
    //.catch(e => console.log(e));
});

app.use('/fs-endpoint',
  express_sharp.expressSharp({
    imageAdapter: new express_sharp.FsAdapter([server_path].join(__dirname, 'images')),
  })
)

const server = app.listen(3000, () => {
  console.log('Listening on port %s...', server.address().port); // string interpolation
});
