<?php
/**
 * PHPUnit bootstrap file
 *
 * @package micropackage/cache
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the package as mu-plugin.
 */
function _manually_load_package() {
	require dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_package' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
