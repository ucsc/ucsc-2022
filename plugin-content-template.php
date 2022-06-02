<?php
/**
 * Template Name: Plugin Content Template
 *
 * @package WordPress
 * @subpackage ucsc-2022
 * @since 1.0
 */

if ( file_exists( get_theme_file_path( 'header-plugin.php' ) ) ) {
	get_header( 'plugin' );
}
if ( file_exists( get_theme_file_path( 'content-plugin.php' ) ) ) {
	get_template_part( 'content', 'plugin' );
}
if ( file_exists( get_theme_file_path( 'footer-plugin.php' ) ) ) {
	get_footer( 'plugin' );
}

