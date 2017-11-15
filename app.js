const express = require('express');

const app = express();


app.use(express.static('public')); //<- it knows to look for the index.html file

const server = app.listen(3000, () => {
  console.log("Listening on port %s...", server.address().port); //string interpolation
})
