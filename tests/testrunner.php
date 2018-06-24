<?php 
/** @file
 * @brief This script runs the PHPUnit tests on a single test file
 * @cond
 */
require 'initialize.php';
// Get the desired test
$test = strip_tags($_REQUEST['test']);
echo 'Test: ' . $test . "\n";

// Run the test
// This fakes the command line parameters to phpunit.phar
$argv1 = array("phpunit.phar");
//$argv[] = '--coverage-html';
//$argv[] = __DIR__ . '/html';
if($test === 'all.php') {
	$argv1[] = __DIR__ . '/phpunit';
} else {
	$argv1[] = $test;
}
$_SERVER['argv'] = $argv1;
$GLOBALS['argv'] = $argv1;

require __DIR__ . '/phpunit.phar';
PHPUnit_TextUI_Command::main();
/// @endcond

