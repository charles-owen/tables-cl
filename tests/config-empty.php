<?php
/**
 * @file
 * Configure file for the database test configuration.
 *
 * Empty template file. Copy to the name config.php and
 * fill in the parameters below.
 */

use CL\Tables\Config;

return function(Config $config) {
	$config->configure('DATABASE-HOST',
		'DATABASE-NAME',
		'DATABASE-USER',
		'DATABASE-PASSWORD',
		'test_tables_cl_');
};
