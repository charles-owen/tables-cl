<?php
/// \cond

use CL\Tables\Config;
use CL\Tables\Table;

/**
 * Fake Table that should not exist
 */
class FakeTable extends Table {
	public function __construct(Config $config) {
		parent::__construct($config, "ymbztqexor");
	}
}

/// \endcond
