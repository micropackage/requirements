<?php
/**
 * Class TestTheme
 *
 * @package micropackage/requirements
 */

namespace Micropackage\Requirements\Test\Checker;

use phpmock\Mock;
use phpmock\MockBuilder;
use Micropackage\Requirements\Requirements;
use Micropackage\Requirements\Checker\Theme as TestedChecker;

/**
 * Theme checker test case.
 */
class TestTheme extends \WP_UnitTestCase {

	use \phpmock\phpunit\PHPMock;

	public function setUp() {
		parent::setUp();
		$this->checker = new TestedChecker();
	}

	public function tearDown() {
		parent::tearDown();
		Mock::disableAll();
	}

	public function bad_params() {
		return [
			[ '5.3' ],
			[ 1 ],
			[ true ],
			[ [ 'slug', 'name' ] ],
			[ [ 'slug' => 'slug', 'name' ] ],
			[ [ 'slug', 'name' => 'name' ] ],
		];
	}

	public function test_get_name_should_return_valid_name() {
		$this->assertSame( 'theme', $this->checker->get_name() );
	}

	/**
	 * @dataProvider bad_params
	 * @expectedException Exception
	 */
	public function test_check_should_throw_exception_if_passed_not_array_with_slug_and_name_keys( $param ) {
		$this->checker->check( $param );
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_check_should_accept_array_with_slug_and_name_requirement() {

		$this->checker->check( [
			'slug' => 'test',
			'name' => 'Test',
		] );

	}

	public function test_check_should_pass_if_current_theme_slug_matches_requirement() {

		$this->checker->check( [
			'slug' => 'default',
			'name' => 'Default',
		] );

		$this->assertEmpty( $this->checker->get_errors() );
	}

	public function test_check_should_fail_if_current_theme_slug_doesnt_match_requirement() {

		$this->checker->check( [
			'slug' => 'not-existing',
			'name' => 'Ups',
		] );

		$errors = $this->checker->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertCount( 1, $errors );
		$this->assertContains( 'Ups', $errors[0] );

	}

}
