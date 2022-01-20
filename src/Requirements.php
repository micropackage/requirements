<?php
/**
 * Requirements class
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements;

use Micropackage\Internationalization\Internationalization;
use Micropackage\Requirements\Interfaces\Checkable;
use Micropackage\Requirements\Checker;

/**
 * Requirements class
 */
class Requirements {

	/**
	 * Plugin display name
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Textdomain
	 *
	 * @var string
	 */
	public static $textdomain = 'micropackage-requirements';

	/**
	 * Requirements array
	 *
	 * @var array
	 */
	protected $requirements = [];

	/**
	 * Checkers array
	 *
	 * @var array
	 */
	protected $checkers = [];

	/**
	 * Errors array
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * If check has been performed
	 *
	 * @var bool
	 */
	private $did_check = false;

	/**
	 * Requirements constructor
	 *
	 * @since 1.0.0
	 * @param string $plugin_name       Plugin display name.
	 * @param array  $requirements      Array with requirements.
	 * @param bool   $autoload_checkers If default checkers should be autoloaded.
	 *                                  Default: true.
	 */
	public function __construct( $plugin_name, $requirements = [], $autoload_checkers = true ) {
		$this->plugin_name = $plugin_name;

		// Add requirements.
		array_map( [ $this, 'add' ], array_keys( $requirements ), $requirements );

		// Register default checkers.
		if ( $autoload_checkers ) {
			$this->load_default_checkers();
		}

		// Load translation.
		$i18n = new Internationalization( 'micropackage-requirements', dirname( __DIR__ ) . '/languages' );
		$i18n->load_translation();
	}

	/**
	 * Loads default checkers
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_default_checkers() {
		array_map(
			[ $this, 'register_checker' ],
			[
				Checker\DocHooks::class,
				Checker\PHP::class,
				Checker\PHPExtensions::class,
				Checker\Plugins::class,
				Checker\SSL::class,
				Checker\Theme::class,
				Checker\WP::class,
			]
		);
	}

	/**
	 * Adds the requirement to collection
	 *
	 * @since  1.0.0
	 * @throws \Exception When requirement with given slug already added.
	 * @param  string $requirement_slug Check slug.
	 * @param  mixed  $checked_value    Value to check.
	 * @return $this
	 */
	public function add( $requirement_slug, $checked_value ) {
		if ( isset( $this->requirements[ $requirement_slug ] ) ) {
			throw new \Exception( sprintf( 'Requirement %s already exists', $requirement_slug ) );
		}

		$this->requirements[ $requirement_slug ] = $checked_value;

		return $this;
	}

	/**
	 * Gets all the requirements
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get() {
		return $this->requirements;
	}

	/**
	 * Registers checker
	 *
	 * @since  1.0.0
	 * @throws \Exception When checker doesn't implement given interface.
	 * @throws \Exception When checker with given name already registered.
	 * @param  mixed $checker Checker class instance or \
	 *                        fully qualified class name.
	 * @return $this
	 */
	public function register_checker( $checker ) {
		$implements = class_implements( $checker );
		$interface  = Checkable::class;

		if ( ! isset( $implements[ $interface ] ) ) {
			throw new \Exception( sprintf( 'Checker must implement %s interface', $interface ) );
		}

		if ( is_string( $checker ) ) {
			$checker = new $checker();
		}

		if ( isset( $this->checkers[ $checker->get_name() ] ) ) {
			throw new \Exception( sprintf( 'Checker %s already exists', $checker->get_name() ) );
		}

		$this->checkers[ $checker->get_name() ] = $checker;

		return $this;
	}

	/**
	 * Checks if the checker has been registered
	 *
	 * @since  1.0.0
	 * @param  string $name Checker name.
	 * @return bool
	 */
	public function has_checker( $name ) {
		return isset( $this->checkers[ $name ] );
	}

	/**
	 * Gets checker instance
	 *
	 * @since  1.0.0
	 * @param  string $name Checker name.
	 * @return false|Checkable
	 */
	public function get_checker( $name ) {
		if ( ! $this->has_checker( $name ) ) {
			return false;
		}

		return $this->checkers[ $name ];
	}

	/**
	 * Checks the requirements
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function check() {
		// Reset state.
		$this->errors = [];

		foreach ( $this->get() as $checker_name => $requirement ) {
			if ( $this->has_checker( $checker_name ) ) {
				call_user_func( [ $this->get_checker( $checker_name ), 'check' ], $requirement );
				$this->errors = array_merge( $this->errors, $this->get_checker( $checker_name )->get_errors() );
			}
		}

		$this->did_check = true;
	}

	/**
	 * Determines if all the requirements has been satisfied
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public function satisfied() {
		if ( ! $this->did_check ) {
			$this->check();
		}

		return empty( $this->errors );
	}

	/**
	 * Returns message with requirements info if any of them is not met.
	 *
	 * @since 1.2.0
	 * @param string|null $message Message to display.
	 * @return string|null Message or null if requirements are met.
	 */
	protected function get_message( $message = null ) {
		if ( $this->satisfied() ) {
			return null;
		}

		if ( ! is_string( $message ) || '' === $message ) {
			return sprintf(
				// Translators: Plugin name.
				__( 'The plugin: <strong>%s</strong> cannot be activated.', self::$textdomain ),
				esc_html( $this->plugin_name )
			);
		}

		return $message;
	}

	/**
	 * Prints notice
	 *
	 * @since  1.0.0
	 * @param string|null $message Message to display.
	 * @return void
	 */
	public function print_notice( $message = null ) {
		$message = $this->get_message( $message );

		if ( null === $message ) {
			return;
		}

		add_action( 'admin_notices', function() use ( $message ) {
			include __DIR__ . '/notice.php';
		} );
	}

	/**
	 * Runs wp_die with proper message if any of the checks failed.
	 * This method shoudl be used interchangeably with `print_notice`.
	 *
	 * @since 1.2.0
	 * @param string|null $message Message to display.
	 * @return void
	 */
	public function kill( $message = null ) {
		$message = $this->get_message( $message );

		if ( null === $message ) {
			return;
		}

		ob_start();

		include __DIR__ . '/die-message.php';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die( ob_get_clean(), wp_strip_all_tags( $message ) );
	}

}
