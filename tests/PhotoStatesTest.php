<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require __DIR__ . '/../src/PhotoStates.php';

class PhotoStatesTest extends TestCase {
  private $dbhandle;
  private $pstates;

  public function setUp(): void {
    $this->dbhandle = new MyDB();
    // $make_table = "DROP TABLE IF EXISTS photoVotes;
    // CREATE TABLE photoVotes (
    //   photoID INTEGER PRIMARY KEY,
    //   upVote INTEGER DEFAULT 0,
    //   downVote INTEGER DEFAULT 0,
    //   groups TEXT
    // );";
    // $this->dbhandle->exec($make_table);
    // $make_data = "INSERT INTO photoVotes (photoID) VALUES (0);";
    //$this->dbhandle->exec($make_data);
    $this->pstates = new PhotoStates();
  }

  public function testAddVotes() {
    $this->AssertFalse($this->pstates->AddVote(-1000, "Up"));
  }
  public function test2 () {
    $this->AssertTrue($this->pstates->AddVote(0, "Down"));
  }
  public function test3 () {
    $this->AssertFalse($this->pstates->AddVote(-1001, "Down"));
  }
  public function test4 () {
    $this->AssertTrue($this->pstates->AddVote(0, "Up"));
  }
}
?>
