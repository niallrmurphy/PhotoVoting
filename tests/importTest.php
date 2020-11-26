<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require __DIR__ . '/../src/Import.php';

class ImportTest extends TestCase {
  private $dbhandle;

  public function setUp(): void {
    $this->dbhandle = new MyDB();
    $drop_table = "DROP TABLE IF EXISTS photoVotes;";
    $make_table = "CREATE TABLE photoVotes (
      photoID INTEGER PRIMARY KEY,
      upVote INTEGER DEFAULT 0,
      downVote INTEGER DEFAULT 0,
      groups TEXT
    );";
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
  }

  public function testImport() {
    $imp = new Importing(null, "./export_list-4.csv");
    $testarray = array(
      0 => array(
        '0' => 'IMG_1910.DNG',
        '1' => 'Stormy Seas',
      ),
      1 => array(
        '0' => 'IMG_1921.DNG',
        '1' => 'Stormy Seas',
      ),
      2 => array(
        '0' => 'IMG_2137.DNG',
        '1' => 'Stormy Seas',
      )
    );
    $this->AssertEquals($imp->parseCSV(), $testarray);
  }
}
?>
