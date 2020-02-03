<?php
/**
 * Checkable interface
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Interfaces;

/**
 * Checkable interface
 */
interface Checkable {

	/**
	 * Gets checker name
	 *
	 * @since  [Next]
	 * @return string
	 */
	public function get_name();

	/**
	 * Checks if the requirement is met
	 *
	 * @since  [Next]
	 * @param  mixed $value Value to check against.
	 * @return void
	 */
	public function check( $value );

}
