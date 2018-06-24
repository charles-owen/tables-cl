<?php
/** @file
 * Base class for database tests.
 */

use CL\Tables\Config;
use CL\Tables\Table;


/** Base class for database tests.
 *
 * Adds some assertions I find useful and a more useful way to add tables
 */
abstract class DatabaseTestBase extends PHPUnit_Extensions_Database_TestCase {

    public function ensureTable(Table $table) {
    	// Drop table if it exists and recreate
	    $pdo = $this->config->pdo;
    	$pdo->query($table->dropSQL());
    	$pdo->query($table->createSQL());
    }

    /**
     * Build a data set from multiple XML files in the db directory.
     *
     * @code
     *   public function getDataSet()
     *   {
     *   return $this->dataSets(array("users.xml", "grades.xml"));
     *   }
     * @endcode
     *
     * @param array $list An array of data set names
     * @return PHPUnit_Extensions_Database_DataSet_CompositeDataSet
     */
    protected function dataSets(array $list) {
        $data = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet();

        foreach($list as $item) {
            $u = $this->createFlatXMLDataSet(__DIR__ . '/../db/' . $item);
            $data->addDataSet($u);
        }

        return $data;
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection() {
    	if($this->config === null) {
		    $this->config = new Config();
		    $filename = __DIR__ . '/../../config.php';
		    if(!file_exists($filename)) {
			    throw new Exception('Database configuration file tests/config.php does not exist.');
		    }
		    $configure = require($filename);
		    if(!is_callable($configure)) {
			    throw new Exception('Database configuration file tests/config.php does not contain a configuration function.');
		    }

		    $configure($this->config);

	        $this->connection = $this->createDefaultDBConnection($this->config->pdo, $this->config->dbname);

	        $this->ensureTables();
	    }

	    return $this->connection;
    }

    public function ensureTables() {

    }


    protected $config = null;
    private $connection = null;
}
