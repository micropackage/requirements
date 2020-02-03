<?php
/**
 * Class TestPHPExtensions
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test\Checker;

use phpmock\Mock;
use Micropackage\Requirements\Requirements;
use Micropackage\Requirements\Checker\PHPExtensions as TestedChecker;

/**
 * PHP checker test case.
 */
class TestPHPExtensions extends \WP_UnitTestCase {

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
		$this->assertSame( 'php_extensions', $this->checker->get_name() );
	}

	/**
	 * @expectedException Exception
	 */
	public function test_check_should_throw_exception_if_passed_not_array_requirement() {
		$this->checker->check( '5.3' );
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_check_should_accept_numeric_or_associative_array_requirement() {

		$extension_loaded = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'extension_loaded' );
		$extension_loaded->expects( $this->any() )->willReturn( true );

		$this->checker->check( [ 'test', 'testing' ] );
		$this->checker->check( [ 'test' => 'test', 'testing' => 'testing' ] );

	}

	public function test_check_should_pass_if_all_extensions_loaded() {

		$extensions = [ 'extension1', 'extension2' ];

		$extension_loaded = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'extension_loaded' );
		$extension_loaded->expects( $this->any() )
						 ->withConsecutive( [ 'extension1' ], [ 'extension2' ] )
						 ->willReturnOnConsecutiveCalls( true, true );

		$this->checker->check( $extensions );

		$this->assertEmpty( $this->checker->get_errors() );

	}

	public function test_check_should_fail_if_at_least_one_extension_not_loaded() {

		$extensions = [ 'extension1', 'extension2' ];

		$extension_loaded = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'extension_loaded' );
		$extension_loaded->expects( $this->exactly( 2 ) )
						 ->withConsecutive( [ 'extension1' ], [ 'extension2' ] )
						 ->willReturnOnConsecutiveCalls( true, false );

		$this->checker->check( $extensions );

		$errors = $this->checker->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertCount( 1, $errors );
		$this->assertContains( 'extension2', $errors[0] );
		$this->assertNotContains( 'extension1', $errors[0] );

	}

	public function test_check_should_fail_if_all_extensions_not_loaded() {

		$extensions = [ 'extension1', 'extension2' ];

		$extension_loaded = $this->getFunctionMock( 'Micropackage\Requirements\Checker', 'extension_loaded' );
		$extension_loaded->expects( $this->exactly( 2 ) )
						 ->withConsecutive( [ 'extension1' ], [ 'extension2' ] )
						 ->willReturnOnConsecutiveCalls( false, false );

		$this->checker->check( $extensions );

		$errors = $this->checker->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertCount( 1, $errors );
		$this->assertContains( 'extension1', $errors[0] );
		$this->assertContains( 'extension2', $errors[0] );

	}

}
