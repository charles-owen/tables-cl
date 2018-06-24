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
     * Supported properties:
     * Property | Documentation
     * ----- | -----
     * prefix | Table name prefix
     * private_key | Private key necessary for JWT
     * public_key | Public key necessary for JWT
     * secret | The site secret value required for new user creation
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
     * Supported properties:
     * Property | Documentation
     * ----- | -----
     * secret | The site secret value required for new user creation
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
        self::$dbpassword = $dbpassword;
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
                    self::$dbpassword);
            } catch(\PDOException $e) {
                throw new TableException("Unable to select database",
	                TableException::NO_CONNECT);
            }
        }

        return $this->pdo;
    }

    //
    // Some of the attributes are made static so they will
    // not display sensitive information when a print_r or
    // var_dump is used on this object
    //
    private $dbhost = null;	    // Database host
	private $dbname = null;     // Database name
    private $dbuser = null;	    // Database user
    private static $dbpassword = null;	// Database password
    private $prefix;            ///< Table prefix

    private $pdo = null;	///< The PDO object
}