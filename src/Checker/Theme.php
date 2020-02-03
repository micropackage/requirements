<?php
/**
 * Theme Checker class
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Checker;

use Micropackage\Requirements\Abstracts;
use Micropackage\Requirements\Requirements;

/**
 * Theme Checker class
 */
class Theme extends Abstracts\Checker {

	/**
	 * Checker name
	 *
	 * @var string
	 */
	protected $name = 'theme';

	/**
	 * Checks if the requirement is met
	 *
	 * @since  1.0.0
	 * @throws \Exception When provided value is not an array with keys: slug, name.
	 * @param  mixed $value Value to check against.
	 * @return void
	 */
	public function check( $value ) {

		if ( ! is_array( $value ) ) {
			throw new \Exception( 'Theme Check requires array parameter with keys: slug, name' );
		}

		if ( ! array_key_exists( 'slug', $value ) || ! array_key_exists( 'name', $value ) ) {
			throw new \Exception( 'Theme Check requires array parameter with keys: slug, name' );
		}

		$theme = wp_get_theme();

		if ( $theme->get_template() !== $value['slug'] ) {
			// Translators: theme name.
			$this->add_error( sprintf( __( 'Required theme: %s', Requirements::$textdomain ), $value['name'] ) );
		}

	}

}
