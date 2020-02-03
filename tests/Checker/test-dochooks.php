<?php
/**
 * Class TestDocHooks
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test\Checker;

use Micropackage\Requirements\Requirements;
use Micropackage\Requirements\Checker\DocHooks as TestedChecker;

/**
 * DocHooks checker test case.
 */
class TestDocHooks extends \WP_UnitTestCase {

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
		$this->assertSame( 'dochooks', $this->checker->get_name() );
	}

	/**
	 * @dataProvider bad_params
	 * @expectedException Exception
	 */
	public function test_check_should_throw_exception_if_passed_not_bool_requirement( $param ) {
		$this->checker->check( $param );
	}

}
