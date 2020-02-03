<?php
/**
 * Class TestPHP
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test\Checker;

use phpmock\Mock;
use Micropackage\Requirements\Requirements;
use Micropackage\Requirements\Checker\PHP as TestedChecker;

/**
 * PHP checker test case.
 */
class TestPHP extends \WP_UnitTestCase {

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
		$this->assertSame( 'php', $this->checker->get_name() );
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

		$phpversion = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'phpversion' );
		$phpversion->expects( $this->any() )->willReturn( '7.0' );

		$this->checker->check( '5.3+dist' );
		$this->checker->check( '5.3' );
		$this->checker->check( 5.3 );
		$this->checker->check( 5 );

	}

	public function test_check_should_pass_when_using_the_same_version() {
		$phpversion = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'phpversion' );
		$phpversion->expects( $this->once() )->willReturn( '7.0.2' );

		$this->checker->check( '7.0.2' );

		$this->assertEmpty( $this->checker->get_errors() );
	}

	public function test_check_should_fail_when_using_lower_version() {
		$phpversion = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'phpversion' );
		$phpversion->expects( $this->once() )->willReturn( '7.0.2' );

		$this->checker->check( '7.1' );

		$this->assertNotEmpty( $this->checker->get_errors() );
	}

}
