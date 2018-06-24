<?php
/**
 * @file
 * View class for the unit testing system and testing home page.
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
	 *    phpunit - Directory containing PHP Unit tests
	 *
	 * @param \Name $name
	 * @param \Value $value
	 */
	public function __set($name, $value) {
		switch($name) {
			case 'phpunit':
				$this->phpunit = $value;
				break;

			default:
				parent::__set($name, $value);
				break;
		}
	}

	public function all() {
	    $html = <<<HTML
<!doctype html>
<html lang=en-US>
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

	private $phpunit = "phpunit";	///< Directory containing PHPUnit tests
}