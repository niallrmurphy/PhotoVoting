const sqlite3 = require('sqlite3').verbose();

// connects db to app;

let db = new sqlite3.Database('./pics.db', (err) => {
  if (err) {
    console.error(err.message);
  }
  console.log('Connected to the pics database.');
});

/**
 * Queries the database to (add/remove) an (up/down) vote on a given photo.
 * @param {number} photo  ID of the photo being voted on
 * @param {string} thumb  'up' if upvote 'down' if downvote
 * @param {boolean} adding true if adding vote, false if removing
 * @returns {Promise} Resolving to object representing the picture with keys upvote and downvote
 */
const addVote = (photo, thumb, adding) => {
  const add = adding ? 1 : -1;

  let sql = `UPDATE
    photoVotes
  SET
    ${thumb}Vote = ${thumb}Vote + ${add}
  WHERE
    photoID = $1`;
  console.log(`${sql}`)
  db.serialize(() => {
    db.run(sql, photo, function(err) {
      if (err) {
        return console.error(err.message);
      }
      console.log(`Row(s) updated: ${this.changes}`);
    });
    return db;
  });
};

const countImages =() => {
  count = db.get('SELECT COUNT(*) FROM photoVotes', (err, row) => {
    if (err) {
      console.log('DB COUNT(*) ERROR', err);
    };
    return row['COUNT(*)']
      ? console.dir(row)
      : console.log(`No count found for photoVotes (this shoudl not happen)`);
  });
};

const createPhotoArray =() => {
  var total_photo_array = Array();
  db.each('SELECT DISTINCT photoID FROM photoVotes', (err, row) => {
    if (err) {
      console.log('DB SELECT DISTINCT ERROR', err);
    };
    var pid = row.photoID;
    total_photo_array.push(pid);
  });
  return total_photo_array;
};

// close the database connection
function cleanup() {
  //db.close((err) => {
  //  if (err) {
  //    return console.error(err.message);
  //  }
  //};
};

const buildImageStructure = () => {
  let sql = 'SELECT * FROM photoVotes';
  db.serialize(() => {
    db.all(sql, [], (err, rows) => {
      if (err) {
        throw err;
      };
      rows.forEach((row) => {
        console.log(row.name);
      });
    });
  });
}

module.exports = { addVote, buildImageStructure, countImages, createPhotoArray };
