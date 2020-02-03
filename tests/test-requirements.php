<?php
/**
 * Class TestRequirements
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test;

use Micropackage\Requirements\Requirements;

/**
 * Requirements test case.
 */
class TestRequirements extends \WP_UnitTestCase {

	public function test_should_return_no_checks_if_not_set() {
		$requirements = new Requirements( 'Test' );
		$this->assertSame( [], $requirements->get() );
	}

	public function test_should_setup_checks_from_constructor() {

		$req = [
			'check1' => 'val1',
			'check2' => 'val2',
		];

		$requirements = new Requirements( 'Test', $req );

		$this->assertSame( $req, $requirements->get() );

	}

	public function test_should_add_1_check_and_return_requirements() {

		$expected = [
			'check1' => uniqid(),
		];

		$requirements = new Requirements( 'Test' );

		$returned = $requirements->add( 'check1', $expected['check1'] );

		$this->assertSame( $requirements, $returned );
		$this->assertSame( $expected, $requirements->get() );

	}

	public function test_should_add_2_checks_and_return_requirements() {

		$expected = [
			'check1' => uniqid(),
			'check2' => 'val2',
		];

		$requirements = new Requirements( 'Test' );

		$returned = $requirements->add( 'check1', $expected['check1'] )
								 ->add( 'check2', $expected['check2'] );

		$this->assertSame( $requirements, $returned );
		$this->assertSame( $expected, $requirements->get() );

	}

	/**
	 * @expectedException Exception
	 */
	public function test_should_throw_exception_if_check_already_added() {

		$requirements = new Requirements( 'Test' );

		$requirements->add( 'check', true )
					 ->add( 'check', true );

	}

	/**
	 * @expectedException Exception
	 */
	public function test_register_checker_should_throw_exception_if_object_checker_does_not_implement_interface() {
		$requirements = new Requirements( 'Test' );
		$requirements->register_checker( new \stdClass() );
	}

	/**
	 * @expectedException Exception
	 */
	public function test_register_checker_should_throw_exception_if_string_checker_does_not_implement_interface() {
		$requirements = new Requirements( 'Test' );
		$requirements->register_checker( 'stdClass' );
	}

	/**
	 * @expectedException Exception
	 */
	public function test_register_checker_should_throw_exception_if_checker_already_registered() {
		$requirements = new Requirements( 'Test', [], false );
		$requirements->register_checker( 'Micropackage\Requirements\Checker\PHP' );
		$requirements->register_checker( 'Micropackage\Requirements\Checker\PHP' );
	}

	public function test_register_checker_should_register_checker_and_return_requirements() {
		$requirements = new Requirements( 'Test', [], false );
		$returned     = $requirements->register_checker( 'Micropackage\Requirements\Checker\PHP' );
		$this->assertTrue( $requirements->has_checker( 'php' ) );
		$this->assertSame( $requirements, $returned );
	}

	public function test_has_checker_should_return_false_if_checker_not_registered() {
		$requirements = new Requirements( 'Test', [], false );
		$this->assertFalse( $requirements->has_checker( 'nope' ) );
	}

	public function test_get_checker_should_return_false_if_checker_not_registered() {
		$requirements = new Requirements( 'Test', [], false );
		$this->assertFalse( $requirements->get_checker( 'nope' ) );
	}

	public function test_get_checker_should_return_checker_instance() {
		$class_name   = 'Micropackage\Requirements\Checker\PHP';
		$requirements = new Requirements( 'Test', [], false );
		$requirements->register_checker( $class_name );
		$this->assertInstanceOf( $class_name, $requirements->get_checker( 'php' ) );
	}

}
