<?php
/**
 * Class TestDocHooks
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test\Checker;

use Micropackage\Requirements\Requirements;
use Micropackage\Requirements\Checker\SSL as TestedChecker;

/**
 * SSL checker test case.
 */
class TestSSL extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->checker = new TestedChecker();
	}

	public function bad_params() {
		return [
			[ '5.3' ],
			[ 1 ],
			[ [ true ] ],
		];
	}

	public function test_get_name_should_return_valid_name() {
		$this->assertSame( 'ssl', $this->checker->get_name() );
	}

	/**
	 * @dataProvider bad_params
	 * @expectedException Exception
	 */
	public function test_check_should_throw_exception_if_passed_not_bool_requirement( $param ) {
		$this->checker->check( $param );
	}

	public function test_check_should_pass_when_enabled_with_ssl() {
		// Override server data.
		$_SERVER['HTTPS']       = '1';
		$_SERVER['SERVER_PORT'] = '443';

		$this->checker->check( true );

		$this->assertEmpty( $this->checker->get_errors() );
	}

	public function test_check_should_pass_when_disabled_without_ssl() {
		// Override server data.
		$_SERVER['HTTPS']       = '0';
		$_SERVER['SERVER_PORT'] = '80';

		$this->checker->check( false );

		$this->assertEmpty( $this->checker->get_errors() );
	}

}
