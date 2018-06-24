<?php
/** @file
 * Unit tests for the class TableWhere
 * @cond
 */

require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . '/cls/DatabaseTestBase.php';
require __DIR__ . '/cls/TestTable.php';

use CL\Tables\Config;
use CL\Tables\Table;


class TableWhereTest extends DatabaseTestBase {

	/**
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet() {
		return $this->dataSets(["test.xml"]);
	}


	public function ensureTables() {
		$this->ensureTable(new TestTable($this->config));
	}


	public function testWhere() {
		$table = new TestTable($this->config);

		$this->addData($table);

		$all = $table->get();
		$this->assertCount(4, $all);

		$data = $table->query([]);
		$this->assertCount(4, $data);

		$data = $table->query(['before' => 117]);
		$this->assertCount(2, $data);

		$data = $table->query(['after'=>93, 'before' => 117]);
		$this->assertCount(1, $data);

		$data = $table->query(['limit' => 3]);
		$this->assertCount(3, $data);
	}

	private function addData($table) {
		$table->add('data 4', 4, 127);
		$table->add('data 1', 27, 93);
		$table->add('data 3', 182, 117);
		$table->add('data 2', 53, 98);
	}
}

/// @endcond
