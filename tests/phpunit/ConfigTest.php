<?php
/** @file
 * Unit tests for the class Config
 * @cond 
 */
require_once __DIR__ . '/../initialize.php';

use CL\Tables\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct() {
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
	}
}

/// @endcond
