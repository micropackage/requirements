<?php
/**
 * Class TestWP
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test\Checker;

use phpmock\Mock;
use Micropackage\Requirements\Requirements;
use Micropackage\Requirements\Checker\WP as TestedChecker;

/**
 * PHP checker test case.
 */
class TestWP extends \WP_UnitTestCase {

	use \phpmock\phpunit\PHPMock;

	public function setUp() {
		parent::setUp();
		$this->checker = new TestedChecker();
	}

	public function tearDown() {
		parent::tearDown();
		Mock::disableAll();
	}

	public function test_get_name_should_return_valid_name() {
		$this->assertSame( 'wp', $this->checker->get_name() );
	}

	/**
	 * @expectedException Exception
	 */
	public function test_check_should_throw_exception_if_passed_not_numeric_or_string_requirement() {
		$this->checker->check( [ '5.3' ] );
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_check_should_accept_numeric_or_string_requirement() {

		$get_bloginfo = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'get_bloginfo' );
		$get_bloginfo->expects( $this->any() )->willReturn( '5.4' );

		$this->checker->check( '5.3+hotfix' );
		$this->checker->check( '5.3' );
		$this->checker->check( 5.3 );
		$this->checker->check( 5 );

	}

	public function test_check_should_pass_when_using_the_same_version() {
		$get_bloginfo = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'get_bloginfo' );
		$get_bloginfo->expects( $this->once() )->willReturn( '5.3.1' );

		$this->checker->check( '5.3.1' );

		$this->assertEmpty( $this->checker->get_errors() );
	}

	public function test_check_should_not_pass_when_using_lower_version() {
		$get_bloginfo = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'get_bloginfo' );
		$get_bloginfo->expects( $this->once() )->willReturn( '5.0.2' );

		$this->checker->check( '5.3' );

		$this->assertNotEmpty( $this->checker->get_errors() );
	}

}
