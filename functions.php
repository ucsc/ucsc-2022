<?php

if ( ! function_exists( 'ucsc_theme_setup' ) ) :

	function ucsc_theme_setup() {

		add_theme_support( 'wp-block-styles' );
		add_editor_style( 'build/style-index.css' );

		/**
		 * Register primary navigation menu location
		 */
		register_nav_menus(
			array(
				'primary' => __( 'Primary Navigation', 'theme-ucsc' ),
			)
		);

		/*
		* Load additional block styles.
		*/
		$styled_blocks = array( 'button', 'post-template', 'post-author', 'site-title', 'query-pagination', 'post-content' );
		foreach ( $styled_blocks as $block_name ) {
			$args = array(
				'handle' => "ucsc-$block_name",
				'src'    => get_theme_file_uri( "wp-blocks/$block_name.css" ),
				$args['path'] = get_theme_file_path( "wp-blocks/$block_name.css" ),
			);
			wp_enqueue_block_style( "core/$block_name", $args );
		}
		/**
		 * Include ThemeHybrid/HyridBreadcrumbs Class
		 * see: https://github.com/themehybrid/hybrid-breadcrumbs
		 * and https://themehybrid.com/weblog/integrating-hybrid-breadcrumbs-into-wordpress-themes
		 */
		if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
			include_once get_parent_theme_file_path( 'vendor/autoload.php' );
		}
		/**
		 * Enqueue Custom Advanced Custom Field Blocks
		 */
		if ( file_exists( get_parent_theme_file_path( 'acf-blocks/register-blocks.php' ) ) ) {
			include_once get_parent_theme_file_path( 'acf-blocks/register-blocks.php' );
		}
	}
endif;
add_action( 'after_setup_theme', 'ucsc_theme_setup' );

/**
 * Enqueue theme scripts and styles.
 */
function ucsc_theme_scripts() {
	 wp_enqueue_style( 'ucsc-theme-styles', get_stylesheet_uri() );
	wp_enqueue_style( 'ucsc-theme-styles-scss', get_template_directory_uri() . '/build/style-index.css' );
}
add_action( 'wp_enqueue_scripts', 'ucsc_theme_scripts' );

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
	 wp_register_script( 'ucsc-admin-scripts', get_template_directory_uri() . '/build/theme.js', array(), '', true );
	wp_enqueue_script( 'ucsc-admin-scripts' );
}

add_action( 'admin_enqueue_scripts', 'ucsc_add_admin_scripts' );

/**
 * Enqueue fonts, styles and scripts.
 */
function ucsc_add_scripts() {
	wp_enqueue_style( 'ucsc-google-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto:100,300,400,500,700&display=swap', false );
	wp_register_script( 'ucsc-front', get_template_directory_uri() . '/build/theme.js', array(), '', true );
	wp_enqueue_script( 'ucsc-front' );
}

add_action( 'wp_enqueue_scripts', 'ucsc_add_scripts' );


/**
 * Copyright shortcode
 * returns copyright symbol and current year
 */
function ucsc_copyright() {
	 $copyright = '&#169;';
	$year       = date( 'Y' );
	return $copyright . $year;
}
add_shortcode( 'copyright', 'ucsc_copyright' );

/**
 * Last Modified Callback Function
 *
 * Create a callback to build the last
 * modified date
 *
 * @return String
 */
function ucsc_last_modified_helper() {
	$ucsc_modified_time = get_the_modified_time( 'U' );
	if ( $ucsc_modified_time > 0 ) {
		return the_modified_time( 'F jS, Y' );
	} else {
		return the_time( 'F jS, Y' );
	}
}

/**
 * Return the last page modification in a readable format
 *
 * This is called by a short code `last-modified`. It looks at the
 * modified time and if that time is greater than zero it returns it
 * as a formatted date. Otherwise, it returns the date the page was created.
 *
 * @return String
 */
function ucsc_last_modified() {
	 ob_start();
	ucsc_last_modified_helper();
	return ob_get_clean();
}
add_shortcode( 'last-modified', 'ucsc_last_modified' );


/**
 * Change Site Title and Logo
 * Main Site vs Subsite
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_logo_switch( $block_content = '', $block = array() ) {
	$siteURL = get_site_url();
	// http://localhost:8888 == www
	if ( $siteURL == '' ) {
		if ( isset( $block['blockName'] ) && 'core/html' === $block['blockName'] ) {
			$html = '';
			return $html;
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
					'<p>
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
		// On all other pages, the site title becomes `p` and page title becomes `h1`
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

		if ( $subtitle != '' && isset( $block['blockName'] ) && 'core/post-title' === $block['blockName'] ) {
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
 * Add campus message meta field
 * Single Blog Post
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_campus_message( $block_content = '', $block = array() ) {
	 // Check for single post; use `global $post` to access data outide the Loop.
	if ( is_single() ) {
		global $post;
		$post_id = get_the_id();
		if ( function_exists( 'get_field' ) ) {
			$cm_select = get_field( 'cm-select' );
		}

		if ( $cm_select ) {
			if ( function_exists( 'get_field' ) ) {
				$cm_fields = get_field( 'cm-fields' );
				$cm_to     = $cm_fields['cm-to'];
				$cm_from   = $cm_fields['cm-from'];
			}
			$cm_class = 'campus-message';

			if ( isset( $block['blockName'] ) && 'core/post-title' === $block['blockName'] ) {
				$html = str_replace( $block_content, $block_content . '<div id="' . $cm_class . '-' . $post_id . '" class="' . $cm_class . '"><p class="campus-message-text cm-to"><span class="campus-message-label">To: </span>' . $cm_to . '</p><p class="campus-message-text cm-from"><span class="campus-message-label">From: </span>' . $cm_from . '</p></div>', $block_content );
				return $html;
			}
			if ( isset( $block['blockName'] ) && 'core/post-author' === $block['blockName'] ) {
				 $html = str_replace( $block_content, '', $block_content );
				 return $html;
			}
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_campus_message', 10, 2 );

/**
 * Breadcrumbs constructor callback helper
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_breadcrumbs_constructor() {
	$labels = array(
		'title' => __( '', 'ucsc' ),
	);
	$args   = array(
		'labels'        => $labels,
		'show_on_front' => true,
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
	if ( is_singular() ) {
		if ( isset( $block['blockName'] ) && 'core/post-title' === $block['blockName'] ) {
			$html = str_replace( $block_content, $breadcrumbs . $block_content, $block_content );
			return $html;
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'ucsc_add_breadcrumbs', 10, 2 );

/**
 *  Filter: link posts
 *  Set link post permalinks to an external URL
 *
 * @param  string $link External link url
 * @param  array  $post Post attributes
 * @return string
 */
function ucsc_link_post_filter( $link, $post ) {
	if ( has_post_format( 'link', $post ) ) {
		if ( function_exists( 'get_field' ) ) {
			$lp_fields  = get_field( 'lp-fields' );
			$lp_source  = $lp_fields['lp-source'];
			$lp_url     = $lp_fields['lp-url'];
			$lp_url_esc = esc_url( $lp_url );
		}

		$link = $lp_url_esc;
	}
	return $link;
}
// add_filter( 'post_link', 'ucsc_link_post_filter', 10, 2 );

/**
* Enqueue developer functions
*/
if ( file_exists( get_theme_file_path( 'utility.php' ) ) ) {
	include get_theme_file_path( 'utility.php' );
}


