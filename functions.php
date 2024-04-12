<?php
/**
 * UCSC functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package UCSC
 * @since UCSC 1.0.0
 */

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
		$styled_blocks = array( 'core/button', 'core/columns', 'core/post-template', 'core/post-author', 'core/site-title', 'core/query-pagination', 'core/post-content', 'core/rss', 'core/post-title', 'core/post-comments', 'core/navigation', 'core/list', 'core/separator', 'core/latest-posts', 'core/quote', 'core/image', 'core/search', 'core/paragraph', 'ucscblocks/accordion', 'core/details','outermost/icon-block' );
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

/**
 * Set accessible site title tags
 * The core/site-title should be an `h1` on the home page and `p` on all other pages.
 * The core/page-title should be an `h1` on all internal pages.
 *
 * @param  string $block_content 	Block content to be rendered.
 * @param  array  $block         	Block attributes.
 */
function ucsc_modify_site_title( $block_content, $block ) {

		// We capture the HTML tag in the site-title block content
		$pattern = '/<(p|h\\d) (.*)<\/(p|h\\d)>/';

    if ( is_front_page() ) {
			// If we're on the home page, we make the site-title an <h1>
			$replacement = '<h1 $2</h1>';
			$block_content = preg_replace($pattern, $replacement, $block_content);

		} else {
			// If we're on an internal page, we make the site-title a <p>
			$replacement = '<p $2</p>';
			$block_content = preg_replace($pattern, $replacement, $block_content);
		}

		// After we modify the site title in the header, we remove this filter to prevent
		// it from running on any other instance of the core/site-title block
		remove_filter( 'render_block_core/site-title', 'ucsc_modify_site_title', 10 );
    return $block_content;
}
add_filter( 'render_block_core/site-title', 'ucsc_modify_site_title', 10, 2 );


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
	if ( is_singular()  ) {
		if ( isset( $block['blockName'] ) && 'core/post-author-name' === $block['blockName'] ) {
			if ( function_exists( 'coauthors_posts_links' ) ) {
				$block_content = coauthors_posts_links( null, null, null, null, false );
			}
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_post_author_link', 8, 2 );

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
		'show_trail_end' => true,
		'container_class'=> 'ucsc-page-header__breadcrumbs'
	);
	return Hybrid\Breadcrumbs\Trail::render( $args );
}


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
		$images->get_sizes();
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
 * Add Truss global header and global footer components
 */
add_action( 'wp_body_open', 'ucsc_add_custom_body_open_code' );

function ucsc_add_custom_body_open_code() {
	if ( function_exists( 'get_field' ) ) {
		$should_use_logo = get_field( 'toggle_universal_logo_visibility', 'options' ) ?? 'true';

		// Convert boolean value to string. Trss header accepts only string value
		if ( is_bool( $should_use_logo ) ) {
			$should_use_logo = json_encode( $should_use_logo );
		}
	}

	echo sprintf( '<trss-ucsc-header use-logo="%s" search-action="/" search-query="s" style="--trss-content-width:80rem;"></trss-ucsc-header>', $should_use_logo ?? 'true' );
}

add_action( 'wp_footer', 'ucsc_add_custom_body_close_code' );

function ucsc_add_custom_body_close_code() {
	echo '<trss-ucsc-footer style="--trss-content-width:80rem;"></trss-ucsc-footer>';
}


add_action( 'wp_footer', 'ucsc_truss_assets' );

function ucsc_truss_assets() { ?>

	<!-- Script and style to include our components library, Truss.  -->
	<script type="module" src="https://unpkg.com/@ucsantacruz/truss@0.7.11/dist/ucsc-trss/ucsc-trss.esm.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/@ucsantacruz/truss@0.7.11/dist/ucsc-trss/ucsc-trss.css">
	<?php
}

add_action( 'wp_footer', 'ucsc_last_modified', 10 );

function ucsc_last_modified() {
	echo '<!-- Last modified code.  --><div class="last-modified__outer" ><div class="last-modified__inner"><div class="last-modified">Last modified: ';
	echo do_shortcode('[last-modified]');
	echo '</div></div></div><!-- End last modified code.  -->';

}
/**
 * Add Excerpts to Pages
 */
add_post_type_support( 'page', 'excerpt' );

/**
 * Remove default skip link and add a custom one.
 */
remove_action( 'wp_footer', 'the_block_template_skip_link' );

/**
 * Override the default skip link to pass a11y for landmarks.
 * @return void
 */
function ucsc_skip_link() {
	/**
	* Print the skip-link styles.
	*/
	?>
	<style id="skip-link-styles">
		.skip-link.screen-reader-text {
			border: 0;
			clip: rect(1px,1px,1px,1px);
			clip-path: inset(50%);
			height: 1px;
			margin: -1px;
			overflow: hidden;
			padding: 0;
			position: absolute !important;
			width: 1px;
			word-wrap: normal !important;
		}

		.skip-link.screen-reader-text:focus {
			background-color: #eee;
			clip: auto !important;
			clip-path: none;
			color: #444;
			display: block;
			font-size: 1em;
			height: auto;
			left: 5px;
			line-height: normal;
			padding: 15px 23px 14px;
			text-decoration: none;
			top: 5px;
			width: auto;
			z-index: 100000;
		}
	</style>

	<?php
	/**
	 * Print the skip-link script.
	 */
	?>
	<script>
		( function() {
			var skipLinkTarget = document.querySelector( 'main' ),
				sibling,
				skipLinkTargetID,
				skipLink;

			// Early exit if a skip-link target can't be located.
			if ( ! skipLinkTarget ) {
				return;
			}

			// Get the site wrapper.
			// The skip-link will be injected in the beginning of it.
			sibling = document.body.firstChild;

			// Early exit if the root element was not found.
			if ( ! sibling ) {
				return;
			}

			// Get the skip-link target's ID, and generate one if it doesn't exist.
			skipLinkTargetID = skipLinkTarget.id;
			if ( ! skipLinkTargetID ) {
				skipLinkTargetID = 'wp--skip-link--target';
				skipLinkTarget.id = skipLinkTargetID;
			}

			// Create a block level element to contain the skip link.
			skipLinkContainer = document.createElement( 'div' );
			skipLinkContainer.setAttribute( 'role', 'navigation' );

			// Create the skip link.
			skipLink = document.createElement( 'a' );
			skipLink.setAttribute( 'aria-label', 'skip to content' );
			skipLink.classList.add( 'skip-link', 'screen-reader-text' );
			skipLink.href = '#' + skipLinkTargetID;
			skipLink.innerHTML = '<?php /* translators: Hidden accessibility text. */ esc_html_e( 'Skip to content' ); ?>';

			skipLinkContainer.append(skipLink);

			// Inject the skip link.
			sibling.parentElement.insertBefore( skipLinkContainer, sibling );
		}() );
	</script>
	<?php
}

add_action('wp_footer', 'ucsc_skip_link');
