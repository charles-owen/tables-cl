<?php
/** @file
 * Unit tests for the class Config
 * @cond 
 */
require_once __DIR__ . '/../initialize.php';
require __DIR__ . '/cls/TestTable.php';

use CL\Tables\Config;
use CL\Tables\TableMaker;

class TestMaker extends TableMaker {
	public function __construct(Config $config) {
		parent::__construct($config);

		$this->add(new TestTable($config));
	}
}

class TableMakerTest extends \PHPUnit_Framework_TestCase
{
	public function testMaker() {
		$config = new Config();
		$filename = __DIR__ . '/../config.php';
		if(!file_exists($filename)) {
			throw new Exception('Database configuration file tests/config.php does not exist.');
		}
		$configure = require($filename);
		if(!is_callable($configure)) {
			throw new Exception('Database configuration file tests/config.php does not contain a configuration function.');
		}

		$configure($config);

		// Can we connect to PDO? (no exception!)
		$pdo = $config->pdo;
		$this->assertNotNull($pdo);

		$maker = new TestMaker($config);
		$this->assertContains("drop table if exists test_tables_cl_test;", $maker->dropSQL());
		$this->assertContains("CREATE TABLE if not exists test_tables_cl_test", $maker->createSQL());

		$sql = <<<SQL
insert into test_tables_cl_test(data, `unique`) values('Stuff', 99);
SQL;

		$pdo->query($sql);

		$this->assertTrue($maker->create(true));
		$sql = <<<SQL
select * from test_tables_cl_test
SQL;

		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$this->assertCount(0, $stmt->fetchAll(\PDO::FETCH_ASSOC));
	}
}

/// @endcond
