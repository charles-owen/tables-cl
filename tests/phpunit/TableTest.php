<?php
/** @file
 * Unit tests for the class Table
 * @cond 
 */

require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . '/cls/DatabaseTestBase.php';
require __DIR__ . '/cls/TestTable.php';

use CL\Tables\Config;
use CL\Tables\Table;

class TableTest extends DatabaseTestBase {

	/**
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet() {
		return $this->dataSets(["test.xml"]);
	}


	public function ensureTables() {
		$this->ensureTable(new TestTable($this->config));
	}


	public function testTransactions() {
		$table = new TestTable($this->config);

		$id = $table->add('data', 27, 93);
		$data = $table->get();
		$this->assertCount(1, $data);
	}
}

/// @endcond
