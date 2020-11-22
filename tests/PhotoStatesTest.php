<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require __DIR__ . '/../src/PhotoStates.php';

class PhotoStatesTest extends TestCase {
  private $dbhandle;
  private $pstates;

  public function setUp(): void {
    $this->dbhandle = new MyDB();
    $drop_table = "DROP TABLE IF EXISTS photoVotes;";
    $make_table = "CREATE TABLE photoVotes (
      photoID INTEGER PRIMARY KEY,
      upVote INTEGER DEFAULT 0,
      downVote INTEGER DEFAULT 0,
      groups TEXT
    );";
    $add_data = Array("INSERT INTO photoVotes (photoID, groups) VALUES (0, '1,2,3');",
      "INSERT INTO photoVotes (photoID, groups) VALUES (1, '2,3,4')");
    // PDO is one beat to the bar (i.e. no multiple SQL statements)
    // so serialize the setup.
    try {
      $stmt = $this->dbhandle->db->query($drop_table);
    } catch (Exception $e) {
      echo $e->getMessage()."\n";
      die ("DB drop table exception in PhotoStatesTest::setUp\n");
    }
    // Make table
    $stmt = $this->dbhandle->db->prepare($make_table);
    if (!$stmt) {
      die ("Could not prepare statement in addVote");
    }
    try {
      $result = $stmt->execute();
      $rows = $stmt->rowCount();
    } catch (Exception $e) {
      echo $e->getMessage()."\n";
      die("DB make table exception in PhotoStatesTest::setUp\n");
    }
    // Make data
    foreach ($add_data as $line) {
      try {
        $stmt = $this->dbhandle->db->query($line);
      } catch (Exception $e) {
        echo $e->getMessage()."\n";
        die ("DB add data exception in PhotoStatesTest::setUp\n");
      }
    }
    // Finally!
    $this->pstates = new PhotoStates();
  }

  public function testAddUpVoteToNonexistent() {
    $this->AssertFalse($this->pstates->AddVote(-1000, "Up"));
  }
  public function testAddDownVoteToExistent () {
    $this->AssertTrue($this->pstates->AddVote(0, "Down"));
  }
  public function testAddDownVoteToNonexistent () {
    $this->AssertFalse($this->pstates->AddVote(-1001, "Down"));
  }
  public function testAddUpVoteToExistent () {
    $this->AssertTrue($this->pstates->AddVote(0, "Up"));
  }
  public function testCountImages() {
    $this->AssertEquals(2, $this->pstates->countImages());
  }
  public function testCreateArray() {
    $this->AssertEquals([0,1], $this->pstates->createPhotoArray());
  }
  public function testGroupsIn() {
    $this->AssertEquals($this->pstates->groupsIn(0), [1,2,3]);
    $this->AssertNotEquals($this->pstates->groupsIn(1), [7,9,3.14159]);
  }
  public function testBuildImageStructure() {
    $this->AssertEquals($this->pstates->buildImageStructure(),
      Array('0' => ['0', '0', '1,2,3'],
            '1' => ['0', '0', '2,3,4']));
  }
  public function testBuildGroups() {
    $this->AssertEquals($this->pstates->buildGroups(),
      ['1' => [0],
       '2' => [0, 1],
       '3' => [0, 1],
       '4' => [1]]
    );
  }
  public function testDecideWhichImages() {
    $this->AssertEquals($this->pstates->decideWhichImages(), [0, 1]);
    $this->AssertEquals($this->pstates->decideWhichImages(null, 1),
      [0,]);
  }
}
?>
