<?php
/**
 * @file
 * Configuration data for database access.
 */

namespace CL\Tables;

/**
 * Configuration data for site.
 */
class Config {

    /**
     * Property getting magic function
     *
     * <b>Properties</b>
     * Property | Type | Description
     * -------- | ---- | -----------
     * prefix | string | Table name prefix
     * pdo | \\PDO | The PDO object
     * dbname | string | The database name (needed for testing)
     *
     * @param string $key Property name
     * @return null|string
     */
    public function __get($key) {
        switch($key) {
            case 'prefix':
                return $this->prefix;

	        case 'pdo':
	        	return $this->pdo();

	        case 'dbname':
		        return $this->dbname;

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
     * Property setting magic function
     *
     * <b>Properties</b>
     * None for now, but maybe in the future?
     *
     * @param $key Property name
     * @param $value Value to set
     */
    public function __set($key, $value) {
        switch($key) {
            default:
	            $trace = debug_backtrace();
	            trigger_error(
		            'Undefined property ' . $key .
		            ' in ' . $trace[0]['file'] .
		            ' on line ' . $trace[0]['line'],
		            E_USER_NOTICE);
	            break;
        }
    }

    /**
     * Configure the database
     * @param $dbhost Database host
     * @param $dbname The database name (used for testing)
     * @param $dbuser Site user account
     * @param $dbpassword Password
     * @param $prefix Table prefix
     */
    public function configure($dbhost, $dbname, $dbuser, $dbpassword, $prefix) {
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpassword = $dbpassword;
        $this->prefix = $prefix;
    }

    /**
     * Database connection object (PDO)
     * @throws APIException if unable to select database
     */
    public function pdo() {
        if($this->pdo === null) {
            try {
                $this->pdo = new \PDO($this->dbhost,
                    $this->dbuser,
                    $this->dbpassword);
	            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch(\PDOException $e) {
                throw new TableException("Unable to select database",
	                TableException::NO_CONNECT);
            }
        }

        return $this->pdo;
    }

	/**
	 * Magic function to disable displaying the database configuration.
	 * @return array Properties to dump
	 */
	public function __debugInfo()
	{
		$properties = get_object_vars($this);
		unset($properties['dbuser']);
		unset($properties['dbhost']);
		unset($properties['dbpassword']);
		unset($properties['pdo']);
		return $properties;
	}

    private $dbhost = null;	    // Database host
	private $dbname = null;     // Database name
    private $dbuser = null;	    // Database user
    private $dbpassword = null;	// Database password
    private $prefix;            ///< Table prefix

    private $pdo = null;	///< The PDO object
}