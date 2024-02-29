<?php declare(strict_types=1);

/*
Plugin Name:  UCSC News Migration
Description:  Allows importing Cascade XML content to WordPress as posts
Author:	   	  Modern Tribe
Version:	  1.0.0
Author URI:
Requires PHP: 8.1
Text Domain:  tribe
*/

namespace TribeUCSC;

use TribeUCSC\DB\DB;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Prevent duplicate autoloading during tests
if ( ! class_exists( Core::class ) ) {
	// Require the vendor folder via multiple locations
	$autoloaders = [
		trailingslashit( __DIR__ ) . 'vendor/scoper-autoload.php',
		trailingslashit( __DIR__ ) . 'vendor/autoload.php',
		trailingslashit( WP_CONTENT_DIR ) . '../vendor/autoload.php',
		trailingslashit( WP_CONTENT_DIR ) . 'vendor/autoload.php',
	];

	$autoload = current( array_filter( $autoloaders, 'file_exists' ) );

	require_once $autoload;
}

register_activation_hook( __FILE__, [ DB::class, 'create_xml_store_table' ] );

function tribe_ucsc(): Core {
	return Core::instance();
}

add_action( 'plugins_loaded', function () {
	tribe_ucsc()->init( __FILE__ );
}, 10, 0 );
