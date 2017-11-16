DROP TABLE IF EXISTS photoVotes;

CREATE TABLE photoVotes (
  id SERIAL,
  photoID INTEGER PRIMARY KEY,
  upVote INTEGER DEFAULT 0,
  downVote INTEGER DEFAULT 0
);