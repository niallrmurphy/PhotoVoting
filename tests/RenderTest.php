<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require __DIR__ . '/../src/Render.php';

class RenderTest extends TestCase {
  private $dbh;
  private $pstates;

  public function setUp(): void {
    $ndb = new MyDB();
    $this->dbh = $ndb->dbh;
    $drop_table = "DROP TABLE IF EXISTS photoVotes;";
    $make_table = "CREATE TABLE photoVotes (
      photoID INTEGER PRIMARY KEY,
      upVote INTEGER DEFAULT 0,
      downVote INTEGER DEFAULT 0,
      imgpath TEXT,
      groups TEXT
    );";
    $add_data = Array("INSERT INTO photoVotes (photoID, groups, imgpath) ".
        "VALUES (0, '1,2,3', 'thing1.jpg');",
      "INSERT INTO photoVotes (photoID, groups, imgpath) ".
        "VALUES (1, '2,3,4', 'thing2.jpg')");
    // PDO is one beat to the bar (i.e. no multiple SQL statements)
    // so serialize the setup.
    try {
      $stmt = $this->dbh->query($drop_table);
    } catch (Exception $e) {
      echo $e->getMessage()."\n";
      die ("DB drop table exception in PhotoStatesTest::setUp\n");
    }
    // Make table
    $stmt = $this->dbh->prepare($make_table);
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
        $stmt = $this->dbh->query($line);
      } catch (Exception $e) {
        echo $e->getMessage()."\n";
        die ("DB add data exception in PhotoStatesTest::setUp\n");
      }
    }
    // Finally!
    $this->r = new Render();
  }

  public function testGenerateImageURI() {
    $this->AssertEquals($this->r->generateImageURI(0), "/img/0");
  }
}
?>
