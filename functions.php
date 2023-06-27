<?php
/**
 * UCSC functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package UCSC
 * @since UCSC 1.0.0
 */

use Image_Sizes;

if ( ! function_exists( 'ucsc_setup' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since UCSC 1.0.0
	 *
	 * @return void
	 */
	function ucsc_setup() {

		add_theme_support( 'wp-block-styles' );
		add_editor_style( 'build/style-index.css' );

		/**
		 * Register primary navigation menu location
		 */
		register_nav_menus(
			array(
				'primary' => __( 'Primary Navigation', 'ucsc-2022' ),
			)
		);

		/*
		* Load additional Core block styles.
		*/
		$styled_blocks = array( 'core/button', 'core/post-template', 'core/post-author', 'core/site-title', 'core/query-pagination', 'core/post-content', 'core/rss', 'core/post-title', 'core/post-comments', 'core/navigation', 'core/list', 'core/separator', 'core/latest-posts', 'core/quote', 'core/image', 'core/search', 'core/paragraph','ucscblocks/accordion' );
		foreach ( $styled_blocks as $block ) {

			$name = explode('/', $block);
			$args = array(
				'handle' => "ucsc-$name[1]",
				'src'    => get_theme_file_uri( "wp-blocks/$name[1].css" ),
				$args['path'] = get_theme_file_path( "wp-blocks/$name[1].css" ),
			);
			wp_enqueue_block_style( $block, $args );
		}

		/**
		 * Include ThemeHybrid/HyridBreadcrumbs Class
		 * see: https://github.com/themehybrid/hybrid-breadcrumbs
		 * and https://themehybrid.com/weblog/integrating-hybrid-breadcrumbs-into-wordpress-themes
		 */
		if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
			include_once get_parent_theme_file_path( 'vendor/autoload.php' );
		}
	}
endif;
add_action( 'after_setup_theme', 'ucsc_setup' );

/**
 * Add Favicons
 * See: https://evilmartians.com/chronicles/how-to-favicon-in-2021-six-files-that-fit-most-needs
 */
function ucsc_favicons() { ?>

	<link rel="shortcut icon" href="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/favicons/favicon-32x32.png" type="image/png" sizes="any">
	<link rel="shortcut icon" href="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/favicons/ucsc-favicon.svg" type="image/svg+xml">
	<link rel="apple-touch-icon" href="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/favicons/apple-icon.png">
	<link rel="manifest" href="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/favicons/manifest.webmanifest">

	<?php
}
add_action( 'wp_head', 'ucsc_favicons' );

/**
 * Enqueue theme scripts and styles.
 */
function ucsc_scripts() {

	wp_enqueue_style( 'ucsc-styles', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_style( 'ucsc-styles-scss', get_template_directory_uri() . '/build/style-index.css', array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_style( 'ucsc-google-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,600,700,800&display=swap', false );
	wp_enqueue_style( 'ucsc-google-roboto-serif-font', 'https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,400;0,8..144,500;0,8..144,600;1,8..144,400;1,8..144,500;1,8..144,600&display=swap', false );
	wp_register_script( 'ucsc-front', get_template_directory_uri() . '/build/theme.js', array(), wp_get_theme()->get( 'Version' ), true );
	wp_enqueue_script( 'ucsc-front' );

}
add_action( 'wp_enqueue_scripts', 'ucsc_scripts' );

/**
 * Enqueue additional Google Font Scripts
 */
function ucsc_googleapi_scripts() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action( 'wp_head', 'ucsc_googleapi_scripts' );

/**
 * Enqueue editor styles and scripts.
 */
function ucsc_add_admin_scripts() {
	wp_register_script( 'ucsc-admin-scripts', get_template_directory_uri() . '/build/theme.js', array(), wp_get_theme()->get( 'Version' ), true );
	wp_enqueue_script( 'ucsc-admin-scripts' );
}
add_action( 'admin_enqueue_scripts', 'ucsc_add_admin_scripts' );

/** TODO #24 Replace hard-coded links */


/** TODO #24 Replace hard-coded links */


/** TODO #24 Replace hard-coded links */

/**
 * Change Site Title and Logo
 * Main Site vs Subsite
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_logo_switch( $block_content = '', $block = array() ) {
	$site_location = home_url();
	if ( '' === $site_location ) {
		if ( isset( $block['blockName'] ) && 'outermost/icon-block' === $block['blockName'] ) {
			if ( isset( $block['attrs']['className'] ) && $block['attrs']['className'] === 'global-header-logo' ) {
					$html = '';
					return $html;
			}
		} elseif ( ( is_front_page() && is_home() ) || is_front_page() || is_home() ) {
			if ( isset( $block['blockName'] ) && 'core/site-title' === $block['blockName'] ) {
				$html = str_replace(
					$block_content,
					'<h1>
						<a href="https://www.ucsc.edu" class="mainsite-logo" id="logo">UC Santa Cruz</a></h1> ',
					$block_content
				);
				return $html;
			}
		} else {
			if ( isset( $block['blockName'] ) && 'core/site-title' === $block['blockName'] ) {
				$html = str_replace(
					$block_content,
					'<p class="mainsite-logo" style="font-size: var(--wp--preset--font-size--xx-large); font-weight: 600;line-height: var(--wp--custom--line-height--small);margin-top: .12em; margin-bottom: .12em;">
						<a href="https://www.ucsc.edu" class="mainsite-logo" id="logo">UC Santa Cruz</a></p> ',
					$block_content
				);
				return $html;
			}
		}
	}
	return $block_content;
}

add_filter( 'render_block', 'ucsc_logo_switch', 10, 2 );

/**
 * Set accessible HTML headers
 * The site title is the `h1` on the home page and `p` on all other pages.
 * The page title is the `h1` on all other pages.
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_adjust_structure( $block_content = '', $block = array() ) {
	if ( is_front_page() ) {
		// On the home page, return the block as is.
		$html = $block_content;
		return $html;
	} else {
		// On all other pages, the site title becomes `p` and page title becomes `h1`.
		if ( isset( $block['blockName'] ) && 'core/site-title' === $block['blockName'] ) {
			$html = str_replace(
				'<h1 ',
				'<p ',
				$block_content
			);
			return $html;
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_adjust_structure', 10, 2 );

/**
 * Add author link to Post Author on
 * Single Blog Post
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_post_author_link( $block_content = '', $block = array() ) {
	// Check for single post; use `global $post` to access data outide the Loop.
	if ( is_single() ) {
		global $post;
		$post_id          = $post->post_id;
		$author_id        = $post->post_author;
		$author_nicename  = get_the_author_meta( 'nicename', $author_id );
		$author_firstname = get_the_author_meta( 'first_name', $author_id );
		$author_lastname  = get_the_author_meta( 'last_name', $author_id );
		if ( $author_firstname && $author_lastname ) {
			$author_name = $author_firstname . ' ' . $author_lastname;
		} else {
			$author_name = $author_nicename;
		}
		$author_email   = get_the_author_meta( 'user_email', $author_id );
		$author_archive = get_author_posts_url( $author_id );
		if ( isset( $block['blockName'] ) && 'core/post-author' === $block['blockName'] ) {
			$html = str_replace( $block_content, '<p class="wp-block-post-author__name">By <a href="' . $author_archive . '">' . $author_name . '</a></p>', $block_content );
			return $html;
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_post_author_link', 10, 2 );

/**
 * Add Subtitle meta field
 * Single Blog Post
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_post_subtitle( $block_content = '', $block = array() ) {
	// Check for single post; use `global $post` to access data outide the Loop.
	if ( is_single() ) {
		global $post;
		$post_id = get_the_id();
		if ( function_exists( 'get_field' ) ) {
			$subtitle = get_field( 'subtitle-copy' );
		}

		if ( '' !== $subtitle && isset( $block['blockName'] ) && 'core/post-title' === $block['blockName'] ) {
			$html = str_replace(
				$block_content,
				$block_content . '<div id="post-subtitle-' . $post_id . '" class="post-subtitle">
					<p class="post-subtitle-text">' . $subtitle . '</p></div>',
				$block_content
			);
			return $html;
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_post_subtitle', 10, 2 );

/**
 * Breadcrumbs constructor callback helper
 *
 * @return string
 */
function ucsc_breadcrumbs_constructor() {
	$labels = array(
		'title' => '',
	);
	$args   = array(
		'labels'         => $labels,
		'show_on_front'  => true,
		'show_trail_end' => false,
		'container_class'=> 'ucsc-page-header__breadcrumbs alignwide'
	);
	return Hybrid\Breadcrumbs\Trail::render( $args );
}

/**
 * Add Breadcrumbs above Post Title.
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_add_breadcrumbs( $block_content = '', $block = array() ) {
	if ( ucsc_breadcrumbs_constructor() ) {
		$breadcrumbs = ucsc_breadcrumbs_constructor();
	}
	if ( is_singular() && isset( $breadcrumbs ) && is_page_template( 'page-no-title-with-breadcrumbs' ) ) {
		if ( isset( $block['blockName'] ) && 'core/post-title' === $block['blockName'] ) {
			if ( isset( $block['attrs']['level'] ) ) {
				$html = str_replace( $block_content, $breadcrumbs . $block_content, $block_content );
				return $html;
			}
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_add_breadcrumbs', 10, 2 );

/**
* Only administrators can move/remove UCSC header and footer regions
* Note: this does not prevent editing of blocks. Only moving/removing
*/
add_filter(
	'block_editor_settings_all',
	function( $settings, $context ) {
		// Allow for the Administrator role and above
				// https://wordpress.org/support/article/roles-and-capabilities/.
		$settings['canLockBlocks'] = current_user_can( 'switch_themes' );

		return $settings;
	},
	10,
	2
);

/**
* Register Advanced Custom Fields
*/
if ( file_exists( get_theme_file_path( 'lib/acf.php' ) ) ) {
	include get_theme_file_path( 'lib/acf.php' );
}

/**
 * Register Block Pattern Customizations
 */
if ( file_exists( get_theme_file_path( 'lib/blocks.php' ) ) ) {
	include get_theme_file_path( 'lib/blocks.php' );
}

/**
 * Register Image Sizes
 */
if ( file_exists( get_theme_file_path( 'lib/image_sizes.php' ) ) ) {
	include get_theme_file_path( 'lib/image_sizes.php' );

	add_action( 'after_setup_theme', function () {
		$images = new Image_Sizes();
		$images->register_sizes();
		$images->register_size_names();
	});
}

/**
 * Enqueue theme block editor style script to modify the "styles" available for blocks in the editor.
 */
function ucsc_block_editor_scripts() {
	wp_enqueue_script( 'ucsc-editor', get_theme_file_uri( '/wp-blocks/styles.js' ), array( 'wp-blocks', 'wp-dom' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'enqueue_block_editor_assets', 'ucsc_block_editor_scripts' );

/**
 * Remove core block patterns
 */
add_action(
	'init',
	function() {
		remove_theme_support( 'core-block-patterns' );
	}
);

/**
 * Add Truss global header and global footer components
 */
add_action( 'wp_body_open', 'ucsc_add_custom_body_open_code' );

function ucsc_add_custom_body_open_code() {
	echo '<trss-ucsc-header use-logo="true" search-action="/" search-query="s" style="--trss-content-width:80rem;"></trss-ucsc-header>';
}

add_action( 'wp_footer', 'ucsc_add_custom_body_close_code' );

function ucsc_add_custom_body_close_code() {
	echo '<trss-ucsc-footer style="--trss-content-width:80rem;"></trss-ucsc-footer>';
}


add_action( 'wp_footer', 'ucsc_truss_assets' );

function ucsc_truss_assets() { ?>

<!-- Script and style to include our components library, Truss.  -->
<script type="module" src="https://unpkg.com/@ucsantacruz/truss@latest/dist/ucsc-trss/ucsc-trss.esm.js"></script>
<link rel="stylesheet" href="https://unpkg.com/@ucsantacruz/truss@latest/dist/ucsc-trss/ucsc-trss.css">

<?php
}
