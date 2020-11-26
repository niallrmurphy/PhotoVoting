DROP TABLE IF EXISTS photoVotes;

CREATE TABLE photoVotes (
  photoID INTEGER PRIMARY KEY,
  upVote INTEGER DEFAULT 0,
  downVote INTEGER DEFAULT 0,
  imgpath TEXT,
  groups TEXT
);
