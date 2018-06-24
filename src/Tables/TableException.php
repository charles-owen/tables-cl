<?php
/**
 * @file
 * Exception class for database operation exceptions.
 */

namespace CL\Tables;

use Throwable;

/**
 * Exception class for database operation exceptions.
 */
class TableException extends \Exception {
	/// Unable to connect to the database
	const NO_CONNECT = 100;

	/// General database read error
	const DATABASE_READ_ERROR = 101;

	/// General error code
	const GENERAL = 0;

	/**
	 * TableException constructor.
	 * @param string $message Message to include
	 * @param int $code Optional code
	 * @param Throwable|null $previous
	 */
	public function __construct($message = "", $code = TableException::GENERAL, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}