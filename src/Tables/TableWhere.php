<?php
/**
 * @file
 * Class that makes it easy to add where options to an SQL query
 */

namespace CL\Tables;

/**
 * Class that makes it easy to add where options to an SQL query
 */
class TableWhere {
    /**
     * TableWhere constructor.
     * @param Table $table Table this is for
     */
    public function __construct(Table $table) {
        $this->table = $table;
    }

	/**
	 * Property get magic method
	 * @param $key Property name
	 *
	 * Properties supported:
	 * where The generated WHERE statement
	 *
	 * @return null|string
	 */
	public function __get($key) {
		switch($key) {
			case "where":
				return $this->where;

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
     * Append a new parameter to where and exec
     * @param $test Test to add to where
     * @param $value Value to sub for ? in test
     * @param int $type Type of parameter (like PDO::PARAM_INT)
     * @param string $op Operator to add test (default="and")
     */
    public function append($test, $value, $type=\PDO::PARAM_STR, $op="and") {
        if($test !== null) {
            if(count($this->wherestack) > 0) {
                if($this->wherestack[0] === '') {
                    $this->wherestack[0] = $test;
                } else {
                    $this->wherestack[0] .= " $op " . $test;
                }
            } else {
                if($this->where === '') {
                    $this->where = "where " . $test;
                } else {
                    $this->where .= " $op " . $test;
                }
            }

        }

        $this->exec[] = $value;
        $this->binds[] = [$value, $type];
    }

    /**
     * Start nesting where conditions. Allows for and/or combinations
     * with parentheticals.
     * @param string $op Operator to append the nesting, default="and"
     */
    public function nest($op="and") {
        array_unshift($this->opstack, $op);
        array_unshift($this->wherestack, '');
    }

    /**
     * End a current nesting
     */
    public function unnest() {
    	if(count($this->wherestack) > 1) {
    		$this->wherestack[1] .= " " . $this->opstack[0] . " (" . $this->wherestack[0] . ")";
	    } else {
		    if(strlen($this->wherestack[0]) > 0) {
			    if($this->where === '') {
				    $this->where = "where (" . $this->wherestack[0] . ")";
			    } else {
				    $this->where .= " " . $this->opstack[0] . " (" . $this->wherestack[0] . ")";
			    }
		    }

	    }

        array_shift($this->opstack);
        array_shift($this->wherestack);
    }

    /**
     * Simple version of sub_sql for use with TableWhere parameterization
     * @param string $sql SQL statement to substitute into
     * @return string
     */
    public function sub_sql($sql) {
        return $this->table->sub_sql($sql, $this->exec);
    }

    /**
     * Execute the SQL statement, filling in the query appropriately.
     * @param $sql SQL statement to execute
     * @return PDOStatement
     * @throws APIException
     */
    public function execute($sql) {
        try {
            $stmt = $this->table->pdo()->prepare($sql);
            for($i=0; $i<count($this->binds); $i++) {
                $bind = $this->binds[$i];
                $stmt->bindValue($i+1, $bind[0], $bind[1]);
            }
            $stmt->execute();
        } catch(\PDOException $exception) {
            throw new TableException("Error reading database", TableException::DATABASE_READ_ERROR);
        }

        return $stmt;
    }

    private $table;

	private $binds = [];
	private $exec = [];
	private $where = '';

    private $opstack = [];
    private $wherestack = [];
}