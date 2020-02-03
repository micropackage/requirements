<?php
/**
 * DocHooks Checker class
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Checker;

use Micropackage\Requirements\Abstracts;
use Micropackage\Requirements\Requirements;

/**
 * DocHooks Checker class
 */
class DocHooks extends Abstracts\Checker {

	/**
	 * Checker name
	 *
	 * @var string
	 */
	protected $name = 'dochooks';

	/**
	 * Checks if the requirement is met
	 *
	 * @since  1.0.0
	 * @throws \Exception When provided value is not a string or numeric.
	 * @param  string $value If dochooks should be enabled or disabled.
	 * @return void
	 */
	public function check( $value ) {

		if ( ! is_bool( $value ) ) {
			throw new \Exception( 'DocHooks Check requires bool parameter' );
		}

		$reflector = new \ReflectionClass( $this );
		$comment   = $reflector->getMethod( 'check' )->getDocComment();

		if ( false === strpos( $comment, '@dochooks-test' ) && $value ) {
			$this->add_error( __( 'Support for DocHooks is required', Requirements::$textdomain ) );
		}

		if ( 0 <= strpos( $comment, '@dochooks-test' ) && ! $value ) {
			$this->add_error( __( 'Support for DocHooks is superfluous', Requirements::$textdomain ) );
		}

	}

}
