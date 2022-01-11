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
    	    // 'primary'   => __('Primary Navigation', 'theme-ucsc'),
    	    'footer' => __('Footer Navigation', 'theme-ucsc')
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
 * Enqueue Google Roboto font.
 */
function ucsc_add_google_fonts() {

wp_enqueue_style( 'ucsc-google-roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,600;0,700;0,900;1,300;1,400;1,600;1,700;1,900', false );
}

add_action( 'wp_enqueue_scripts', 'ucsc_add_google_fonts' );


/**
 * Temporary function -- shortcode to list locations.
 */
function jc_nav_menus() {
	$menus = get_registered_nav_menus();

	foreach ( $menus as $location => $description ) {

		return $location . ': ' . $description . '<br />';
	}
}

add_shortcode( 'listmenus', 'jc_nav_menus' );


function jc_howdy(){
 $locations = get_theme_mod('nav_menu_locations');

	foreach ( $locations as $location ) {

		return $location . '<br />';
	}
}

add_shortcode( 'howdy', 'jc_howdy' );

/**
 *
 * Automatically generate footer legal menu upon theme activation
 *
 */

/**
 * Menu item generator
 * Automatically generate custom link that points to url parameter
 */
function ucsc_generate_site_nav_menu_item( $term_id, $title, $url ) {

    wp_update_nav_menu_item($term_id, 0, array(
        'menu-item-title'   =>  sprintf( __('%s', 'theme-ucsc'), $title ),
        'menu-item-url'     =>  $url,
        'menu-item-status'  =>  'publish'
    ) );

}

/**
 * Menu generator function
 */
function ucsc_generate_site_nav_menu( $menu_name, $menu_items_array, $location_target ) {

    $menu_footer = $menu_name;
    wp_create_nav_menu( $menu_footer );
    $menu_footer_obj = get_term_by( 'name', $menu_footer, 'nav_menu' );

    foreach( $menu_items_array as $page_name => $page_location ){
        ucsc_generate_site_nav_menu_item( $menu_footer_obj->term_id, $page_name, $page_location );
    }

    $locations_primary_arr = get_theme_mod( 'nav_menu_locations' );
    $locations_primary_arr[$location_target] = $menu_footer_obj->term_id;
    set_theme_mod( 'nav_menu_locations', $locations_primary_arr );

    update_option( 'menu_check', true );

}


/**
 * Runs when user switches to your custom theme
 *
 */
function ucsc_after_switch_theme() {
/**
 * Setup the site navigation
 */
    $run_menu_maker_once = get_option('menu_check');

    if ( ! $run_menu_maker_once ){
        /**
         * Setup Navigation for : Footer Menu - Legal
         */
        $footer_menu_items = array(
            'Accreditation'  =>  'https://academicaffairs.ucsc.edu/accreditation/',
            'Non-Discrimination Policy' =>  'https://diversity.ucsc.edu/eeo-aa/images/non-discrimination-policy.pdf',
            'Land Acknowledgment'  =>  'https://www.ucsc.edu/land-acknowledgement/index.html',
            'Employment'   =>  'https://www.ucsc.edu/about/employment.html',
            'Privacy Policy & Terms of Use'    =>  'https://its.ucsc.edu/terms/',
			'Sexual Violence Prevention & Response (Title IX)' => 'https://titleix.ucsc.edu/index.html'
        );
        ucsc_generate_site_nav_menu( 'Footer Menu', $footer_menu_items, 'footer' );
    }
}
add_action( 'after_switch_theme', 'ucsc_after_switch_theme');
