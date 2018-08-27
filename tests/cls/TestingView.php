<?php
/**
 * @file
 * View class for the unit testing system and testing home page.
 * \cond
 */

namespace Testing;

/**
 * View class for the unit testing system and testing home page.
 */
class TestingView {
	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Magic property set function.
	 *
	 * Properties supported:
	 *
	 * Property | Description
	 * -------- | -----------
	 * phpunit  | Directory containing PHP Unit tests
	 *
	 * @param string $key
	 * @param $value
	 */
	public function __set($key, $value) {
		switch($key) {
			case 'phpunit':
				$this->phpunit = $value;
				break;

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
	 * Present all of the HTML for the page
	 * @return string HTML
	 */
	public function all() {
	    $html = <<<HTML
<!doctype html>
<html lang="en-US">
<head>
<title>Testing</title>
<link href="tests.css" type="text/css" rel="stylesheet" />
<script src="jquery-3.3.1.min.js"></script>
<script src="tests.js"></script>
</head>
<body>
<div class="tests">
HTML;

	    $html .= $this->present();

	    $html .= <<<HTML
</div>
</body>
</html>
HTML;
	    return $html;
    }


	/**
	 * Present the body of the HTML page
	 * @return string HTML
	 */
	public function present() {
		$html = '';

		if($this->phpunit !== null) {
			// This finds all of the tests in the tests directory and
			// adds them to the testing object.
			$files = scandir($this->phpunit);
			$tests = "var tests = new Tests('testsdiv', 'resultsdiv');\n";
			// Some files we ignore...
			$ignore = array('EmptyTest.php', 'EmptyDBTest.php');
			foreach($files as $file) {
				if(in_array($file, $ignore)) {
					continue;
				}

				if(strpos($file, 'Test.php') !== FALSE) {
					$test = substr($file, 0, strlen($file) - 8);
					$tests .= "tests.add('$test');\n";
				}
			}

			$tests .= 'tests.display();';

			$html .= <<<HTML
<nav><a href="#results">results</a></nav>
<h1>PHPUnit Testing</h1>

<!-- Place to put the tests -->
<div id="testsdiv"></div>

<!-- Results form -->
<form><h2 id="results">Results
<a href="#" class="clearresults">clear</a>
<span class="verbose"><input type="checkbox" name="verbose" id="verbose">verbose</span>
</h2></form>
<div id="resultsdiv"></div>

<script>
// The testing object 
$tests
</script>
HTML;

		}

		return $html;
	}

	/// Directory containing PHPUnit tests
	private $phpunit = "phpunit";
}

/// \endcond