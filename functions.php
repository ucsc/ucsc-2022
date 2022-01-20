<?php
if ( ! function_exists( 'ucsc_theme_setup' ) ) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which runs
     * before the init hook. The init hook is too late for some features, such as indicating
     * support for post thumbnails.
     */
    function ucsc_theme_setup() {
        /**
         * Add default posts and comments RSS feed links to <head>.
         */
        add_theme_support( 'automatic-feed-links' );

        /**
         * Enable support for post thumbnails and featured images.
         */
        add_theme_support( 'post-thumbnails' );

        add_theme_support( 'editor-styles' );

		add_editor_style( 'build/index.css' );

        add_theme_support( 'wp-block-styles' );


		/**
    	 * Register Footer menu location
    	 */
    	register_nav_menus( array(
    	    'primary'   => __('Primary Navigation', 'theme-ucsc'),
    	) );
    }
endif;
add_action( 'after_setup_theme', 'ucsc_theme_setup' );

/**
 * Enqueue theme scripts and styles.
 */
function ucsc_theme_scripts() {
    wp_enqueue_style( 'ucsc-theme-styles', get_stylesheet_uri() );
	wp_enqueue_style('ucsc-theme-styles-scss', get_template_directory_uri(). '/build/style-index.css');
}
add_action( 'wp_enqueue_scripts', 'ucsc_theme_scripts' );

/**
 * Enqueue additional Google Font Scripts
 */
function ucsc_googleapi_scripts(){
echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action('wp_head','ucsc_googleapi_scripts');

/**
 * Enqueue Google Roboto and Roboto Condensed fonts.
 */

function ucsc_add_scripts() {

wp_enqueue_style( 'ucsc-google-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,600;0,700;0,900;1,300;1,400;1,600;1,700;1,900', false );
wp_enqueue_style( 'ucsc-google-roboto-condensed-font', 'https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,600;1,600&display=swap', false );
wp_register_script( 'ucsc-front', get_template_directory_uri() . '/build/front.js', array(), '1.0.0', false );
wp_enqueue_script( 'ucsc-front' );
}

add_action( 'wp_enqueue_scripts', 'ucsc_add_scripts' );



/**
 * Copyright shortcode
 * returns copyright symbol and current year
 */
function ucsc_copyright(){
	$copyright = '&#169;';
	$year = date('Y');
	return $copyright.$year;
}
add_shortcode( 'copyright', 'ucsc_copyright' );

/**
 * Last Modified shortcode
 * returns the date of the
 * last time the page was
 * updated
 */
function ucsc_last_modified_helper(){
	$ucsc_time = get_the_time('U');
    $ucsc_modified_time = get_the_modified_time('U');
	if ($ucsc_modified_time >= $ucsc_time + 86400) {
        return the_modified_time('F jS, Y');
	}
}
function ucsc_last_modified(){
	ob_start();
	ucsc_last_modified_helper();
	return ob_get_clean();
}
add_shortcode( 'last-modified', 'ucsc_last_modified' );


