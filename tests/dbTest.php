<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require __DIR__ . '/../db/db.php';

class dbTest extends TestCase {
  private $dbhandle;
  private $pstates;

  public function setUp(): void {
    $this->$dbhandle = new MyDB();
    $make_table = "DROP TABLE IF EXISTS photoVotes;
    CREATE TABLE photoVotes (
      photoID INTEGER PRIMARY KEY,
      upVote INTEGER DEFAULT 0,
      downVote INTEGER DEFAULT 0,
      groups TEXT
    );";
    $this->$dbhandle->exec($make_table);
    $make_data = "INSERT INTO photoVotes (photoID) VALUES (0);";
    $this->$dbhandle->exec($make_data);
  }

  public function testconstruct() {
    $this->assertNotNull($this->$dbhandle);
    $this->assertNotInstanceOf(RuntimeException::class,
      $this->$dbhandle->TestForSuccess());
  }

}
?>
