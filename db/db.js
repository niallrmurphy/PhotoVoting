const pg = require('pg-promise')(); // default options = ();

// connects db to app;
const connectionString = `postgres://${process.env.USER}:@localhost:5432/photo_votes`;
const db = pg(connectionString);

/**
 * Queries the database to (add/remove) an (up/down) vote on a given photo.
 * @param {number} photo  ID of the photo being voted on
 * @param {string} thumb  'up' if upvote 'down' if downvote
 * @param {boolean} adding true if adding vote, false if removing
 * @returns {Promise} Resolving to object representing the picture with keys upvote and downvote
 */
const addVote = (photo, thumb, adding) => {
  const add = adding ? 1 : -1;
  return db.one(`
    UPDATE
      photoVotes
    SET
      ${thumb}Vote = (SELECT ${thumb}Vote FROM photoVotes WHERE photoID = $1) + ${add}
    WHERE
      photoID = $1
    RETURNING *
    `, // uses subquery to select current value from table, and increments by one downvote.
  photo);
};

module.exports = { addVote };
