<?php

use Fr\LS;

class TestUserBasic extends PHPUnit_Framework_TestCase {

  private static $pdo = null;

  /**
   * @var LS
   */
  private $LS;

  public function setUp(){
    self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], array(
      \PDO::ATTR_PERSISTENT => true,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ));

    $config = array(
      "db" => array(
        "type" => $GLOBALS['DB_TYPE'],
        "host" => isset($GLOBALS['DB_HOST']) ? $GLOBALS['DB_HOST'] : null,
        "port" => isset($GLOBALS['DB_PORT']) ? $GLOBALS['DB_PORT'] : null,
        "username" => $GLOBALS['DB_USERNAME'],
        "password" => $GLOBALS['DB_PASSWORD'],
        "name" => $GLOBALS['DB_NAME']
      ),
      "features" => array(
        "auto_init" => false,
        "start_session" => false
      )
    );

    if($GLOBALS['DB_TYPE'] === "sqlite"){
      $config["db"]["sqlite_path"] = $GLOBALS['DB_SQLITE_PATH'];
    }

    $this->LS = new LS($config);
  }

  public function testUserRegister(){
    $info = array(
      "email" => "test@test.com",
      "name" => "ABC",
      "created" => date("Y-m-d H:i:s")
    );
    $this->LS->register("test", "abc", $info);

    $sth = self::$pdo->query("SELECT * FROM users WHERE id = '1'");
    $r = $sth->fetch(\PDO::FETCH_ASSOC);

    $this->assertEquals("test", $r["username"]);
    $this->assertEquals("test@test.com", $r["email"]);
    $this->assertEquals($info["name"], $r["name"]);
    $this->assertEquals($info["created"], $r["created"]);
  }

  public function testUserInfo(){
    $user = $this->LS->getUser("*", 1);

    $sth = self::$pdo->query("SELECT * FROM users WHERE id = '1'");
    $r = $sth->fetch(\PDO::FETCH_ASSOC);

    $this->assertEquals($r["username"], $user["username"]);
    $this->assertEquals($r["email"], $user["email"]);
    $this->assertEquals($r["name"], $user["name"]);
    $this->assertEquals($r["created"], $user["created"]);
  }

  public static function tearDownAfterClass(){
    self::$pdo->exec("DROP TABLE users;DROP TABLE user_devices;DROP TABLE user_tokens;");
  }

}
