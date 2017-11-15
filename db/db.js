const pg = require('pg-promise'){} // default options = {};

const connectionString = "postgres://process.env.USER:@localhost:5432/photo_votes" //connects db to app;
const db = pg(connectionString);

const addDownVote = (photo) => {
  return db.one(`
    UPDATE
      photoVotes
    SET
      downVote = (SELECT downVote FROM photoVotes WHERE photoID = $1) + 1
    WHERE
      photoID = $1
    `, // uses subquery to select current value from table, and increments by one downvote.
  photo )
};

const addUpVote = (photo) => {
  return db.one(`
    UPDATE
      photoVotes
    SET
      upVote = (SELECT upVote FROM photoVotes WHERE photoID = $1) + 1
    WHERE
      photoID = $1
    `, // uses subquery to select current value from table, and increments by one upvote.
  photo )
};

const totalVotes = (photo) => {
  return db.one(`
    SELECT
      upVote, downVote
    FROM
      photoVotes
    WHERE
      photoID = $1
  `,
  photo )
};
