<?php
/**
 * @file
 * Abstract base class for classes that provide the SQL create and drop table statements
 */

namespace CL\Tables;

/**
 * Abstract base class for classes that provide the SQL create and drop table statements
 *
 * Also includes the function to actually create or drop and create the tables.
 *
 * A TableMaker has a list of table objects to create and a list of other TableMaker objects to use
 */
abstract class TableMaker {
    /**
     * Constructor
     * @param Config $config Database configuration object
     */
    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * Add a Table object to the list of tables to create
     * @param Table $table
     */
    protected function add(Table $table) {
        $this->tables[] = $table;
    }

    /**
     * Add a TableMaker object to the list of tables to create
     * @param TableMaker $maker
     */
    protected function add_maker(TableMaker $maker) {
        $this->makers[] = $maker;
    }

    /**
     * The PDO object to access the database
     */
    public function pdo() {
    	return $this->config->pdo();
    }

    /**
     * Create an SQL create table command for a collection of tables
     */
    public function createSQL() {
        $sql = '';

        foreach($this->tables as $table) {
            $sql .= $table->createSQL() . "\n";
        }

        foreach($this->makers as $maker) {
            $sql .= $maker->createSQL() . "\n";
        }

        return $sql;
    }

    /**
     * Create the appropriate SQL DROP TABLE command to drop
     * a collection of tables.
     */
    public function dropSQL() {
        $sql = '';

        for($i=count($this->makers)-1; $i>=0; $i--) {
            $sql .= $this->makers[$i]->dropSQL() . "\n";
        }

        for($i=count($this->tables)-1; $i>=0; $i--) {
            $sql .= $this->tables[$i]->dropSQL() . "\n";
        }

        return $sql;
    }

    /**
     * Create the table using this maker
     * @param $drop true if we should drop the table first if it exists
     * @return true if successful
     */
    public function create($drop) {
        $pdo = $this->pdo();

        if($drop) {
            if($pdo->query($this->dropSQL()) === false) {
                return false;
            }
        }

        if($pdo->query($this->createSQL()) === false) {
            return false;
        }

        return $this->alter();
    }

    /**
     * Alter existing tables
     */
    public function alter() {
        foreach($this->tables as $table) {
            $table->alter();
        }

        foreach($this->makers as $maker) {
            $maker->alter();
        }

        return true;
    }

    private $config;
    private $tables = array();
    private $makers = array();
}