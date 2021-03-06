<?php
/** @file
 * Base class for all table model classes
 */

/// Classes in the cl/tables package
namespace CL\Tables;

use \PDO;

/**
 * Base class for all table model classes
 *
 * @cond
 * @property PDO pdo
 * @property string prefix Table names prefix
 * @property string tablename The Table name
 * @endcond
 */
abstract class Table {
	/** Constructor
	 * @param Config $config Database configuration object
	 * @param string $name Table base name to use.
	 */
	public function __construct(Config $config, $name) {
        $this->config = $config;
		$this->tablename = $config->prefix . $name;
		$this->tableprefix = $config->prefix;
	}


	/**
	 * Property get magic method
	 * @param string $key Property name
	 *
	 * Properties supported:
	 * tablename The table name
	 * prefix The table prefix
	 * pdo The PDO object
	 *
	 * @return null|string
	 * @throws TableException on error
	 */
	public function __get($key) {
		switch($key) {
			case "tablename":
				return $this->tablename;

			case "prefix":
				return $this->prefix;

			case "pdo":
				return $this->config->pdo();

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property ' . $key .
					' in ' . $trace[0]['file'] .
					' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
				return null;
		}
	}

	/**
	 * Property set magic method
	 * @param string $key Property name
	 * @param mixed $value Value to set
	 */
	public function __set($key, $value) {
		$trace = debug_backtrace();
		trigger_error(
			'Undefined property ' . $key .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);
	}

	/**
	 * Diagnostic routine that substitutes into an SQL statement
	 * @param string $query The query with : or ? parameters
	 * @param array $params The arguments to substitute
	 * @return string Query as a string
	 */
	public function sub_sql($query, $params) {
        $keys = array();
        $values = array();
        
        # build a regular expression for each parameter
        foreach ($params as $key=>$value)
        {
            if (is_string($key))
            {
                $keys[] = '/:'.$key.'/';
            }
            else
            {
                $keys[] = '/[?]/';
            }
            
            if(is_numeric($value))
            {
                $values[] = intval($value);
            }
            else
            {
                $values[] = '"'.$value .'"';
            }
        }
        
        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;		
	}

	/**
	 * Get the PDO object
	 * @return PDO object
	 * @throws TableException
	 */
    public function pdo() {
        return $this->config->pdo();
    }

	/**
	 * Check to see if the table exists
	 * @return boolean true if it does
	 */
    public function exists() {
    	$sql = <<<SQL
select 1 from $this->tablename LIMIT 1
SQL;

	    $stmt = $this->config->pdo->prepare($sql);
	    try {
		    if($stmt->execute() === false) {
			    return false;
		    }
	    } catch(\PDOException $exception) {
		    return false;
	    }

	    return true;
    }

    /**
     * Convert a system time to the format for storing in the database
     * @param int $time Time convert. If omitted, use the current time.
     * @return string Time as a string
     */
    public static function timeStr($time=null) {
        if($time === null) {
            $time = time();
        }

        return date("Y-m-d H:i:s", $time);
    }


	/**
	 * Check if a named index exits in a table
	 * @param string $name
	 * @return bool true if named exit exists
	 * @throws TableException
	 */
	public function indexExists($name) {
		$pdo = $this->pdo();
		$sql = "show index from $this->tablename";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();

		foreach($stmt as $row) {
			if($row['Key_name'] === $name) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Create the appropriate SQL CREATE TABLE command to create the table.
	 * @return null|string CREATE TABLE command or null if none
	 */
	public function createSQL() {
		return null;
	}

	/**
	 * Create the appropriate SQL DROP TABLE command to drop the table.
	 * @return string SQL
	 */
	public function dropSQL() {
		return <<<SQL
drop table if exists $this->tablename;
SQL;
	}

	/**
	 * Table alteration functions that may be needed in some cases
	 *
	 * This is used to add indices to existing tables
	 * Used by TableMaker class only.
	 */
	public function alter() {}

	/**
	 * Table cleaning. Override for tables that have some
	 * cleaning functionality.
	 * @return string Text result of cleaning of null if not implemented.
	 */
	public function clean() {
		return null;
	}


	protected $config;      ///< The configuration object
	protected $tableprefix;	///< Table name prefix
	protected $tablename;	///< Table name to use
}

