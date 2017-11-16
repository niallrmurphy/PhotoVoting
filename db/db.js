const pg = require('pg-promise')(); // default options = ();

// connects db to app;
const connectionString = `postgres://${process.env.USER}:@localhost:5432/photo_votes`;
const db = pg(connectionString);

const addDownVote = photo =>
  db.none(`
    UPDATE
      photoVotes
    SET
      downVote = (SELECT downVote FROM photoVotes WHERE photoID = $1) + 1
    WHERE
      photoID = $1
    `, // uses subquery to select current value from table, and increments by one downvote.
    photo);


const addUpVote = photo =>
  db.none(`
    UPDATE
      photoVotes
    SET
      upVote = (SELECT upVote FROM photoVotes WHERE photoID = $1) + 1
    WHERE
      photoID = $1
    `, // uses subquery to select current value from table, and increments by one upvote.
    photo);


const totalVotes = photo =>
  db.one(`
    SELECT
      *
    FROM
      photoVotes
    WHERE
      photoID = $1
  `, photo);

module.exports = {
  totalVotes,
  addUpVote,
  addDownVote,
};
