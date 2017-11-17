const pg = require('pg-promise')(); // default options = ();

// connects db to app;
const connectionString = `postgres://${process.env.USER}:@localhost:5432/photo_votes`;
const db = pg(connectionString);

const addVote = (photo, thumb) =>
  db.one(`
    UPDATE
      photoVotes
    SET
      ${thumb}Vote = (SELECT ${thumb}Vote FROM photoVotes WHERE photoID = $1) + 1
    WHERE
      photoID = $1
    RETURNING *
    `, // uses subquery to select current value from table, and increments by one downvote.
  photo);

module.exports = { addVote };
