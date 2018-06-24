<?php
/** @file
 * The main unit testing page
 * @author Charles B. Owen
 * @cond
 */
require 'initialize.php';
$view = new Testing\TestingView();
echo $view->all();
?>

<?php
/// @endcond
