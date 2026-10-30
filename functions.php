<?php

/**
 * UCSC functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package UCSC
 * @since UCSC 1.0.0
 */

if (!function_exists('ucsc_setup')):
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since UCSC 1.0.0
	 *
	 * @return void
	 */
	function ucsc_setup()
	{
		add_theme_support('wp-block-styles');
		add_editor_style('build/style-index.css');

		/**
		 * Register primary navigation menu location
		 */
		register_nav_menus([
			'primary' => __('Primary Navigation', 'ucsc-2022'),
		]);

		/*
		 * Load additional Core block styles.
		 */
		$styled_blocks = [
			'core/button',
			'core/columns',
			'core/cover',
			'core/post-template',
			'core/post-author',
			'core/site-title',
			'core/query-pagination',
			'core/post-content',
			'core/rss',
			'core/post-title',
			'core/post-comments',
			'core/navigation',
			'core/list',
			'core/separator',
			'core/latest-posts',
			'core/quote',
			'core/image',
			'core/search',
			'core/paragraph',
			'ucscblocks/accordion',
			'core/details',
			'outermost/icon-block',
		];
		foreach ($styled_blocks as $block) {
			$name = explode('/', $block);
			$args = [
				'handle' => "ucsc-$name[1]",
				'src' => get_theme_file_uri("wp-blocks/$name[1].css"),
				'path' => get_theme_file_path("wp-blocks/$name[1].css"),
			];
			wp_enqueue_block_style($block, $args);
		}

		/**
		 * Include ThemeHybrid/HyridBreadcrumbs Class
		 * see: https://github.com/themehybrid/hybrid-breadcrumbs
		 * and https://themehybrid.com/weblog/integrating-hybrid-breadcrumbs-into-wordpress-themes
		 */
		if (file_exists(get_parent_theme_file_path('vendor/autoload.php'))) {
			include_once get_parent_theme_file_path('vendor/autoload.php');
		}

		/**
		 * Register Advanced Custom Fields
		 */
		if (file_exists(get_theme_file_path('lib/acf.php'))) {
			include get_theme_file_path('lib/acf.php');
		}

		/**
		 * Register Block Pattern Customizations
		 */
		if (file_exists(get_theme_file_path('lib/blocks.php'))) {
			include get_theme_file_path('lib/blocks.php');
		}

	}
endif;
add_action('after_setup_theme', 'ucsc_setup');

/**
 * Enable automatic theme updates from GitHub releases.
 * Compares the Version in style.css against the latest GitHub release tag.
 * see: https://github.com/YahnisElsts/plugin-update-checker
 */
if (file_exists(get_parent_theme_file_path('vendor/autoload.php'))) {
	require_once get_parent_theme_file_path('vendor/autoload.php');

	// Update checks only matter in the dashboard and during cron, so skip the
	// work on front-end requests. wp_is_auto_update_enabled_for_type() lives in
	// an admin-only file, so make sure it's loaded before calling it; it returns
	// false whenever the site has disabled updates (AUTOMATIC_UPDATER_DISABLED /
	// WP_AUTO_UPDATE_CORE constants, automatic_updater_disabled / auto_update_theme
	// filters, host overrides, etc.).
	if ((is_admin() || wp_doing_cron()) && class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory')) {
		if (!function_exists('wp_is_auto_update_enabled_for_type')) {
			require_once ABSPATH . 'wp-admin/includes/update.php';
		}

		if (wp_is_auto_update_enabled_for_type('theme')) {
			$ucsc_update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
				'https://github.com/ucsc/ucsc-2022/',
				get_parent_theme_file_path('style.css'),
				'ucsc-2022'
			);
			// Use the release asset (ucsc-2022.zip) instead of the source archive
			$ucsc_update_checker->getVcsApi()->enableReleaseAssets('/ucsc-2022\.zip/');
		}
	}
}

/**
 * Add SVG favicon and web manifest alongside core's Site Icon output.
 * Core's wp_site_icon() emits PNG/apple-touch-icon from the uploaded Site Icon;
 * this filter supplements it with the SVG icon and the manifest.
 */
function ucsc_site_icon_meta_tags($tags)
{
	$tags[] = sprintf(
		'<link rel="icon" href="%s" type="image/svg+xml">',
		esc_url(get_theme_file_uri('images/favicons/ucsc-favicon.svg'))
	);
	$tags[] = sprintf(
		'<link rel="manifest" href="%s">',
		esc_url(get_theme_file_uri('images/favicons/manifest.webmanifest'))
	);
	return $tags;
}
add_filter('wp_site_icon_meta_tags', 'ucsc_site_icon_meta_tags');

/**
 * Ship theme default favicons when no Site Icon is configured.
 * Uploading a Site Icon at Appearance → Customize → Site Identity overrides these.
 */
function ucsc_default_favicons()
{
	if (has_site_icon()) {
		return;
	}
	$favicon_png = esc_url(get_theme_file_uri('images/favicons/favicon-32x32.png'));
	$favicon_svg = esc_url(get_theme_file_uri('images/favicons/ucsc-favicon.svg'));
	$apple_icon  = esc_url(get_theme_file_uri('images/favicons/apple-icon.png'));
	$manifest    = esc_url(get_theme_file_uri('images/favicons/manifest.webmanifest'));
	printf('<link rel="icon" href="%s" sizes="32x32" type="image/png">' . "\n", $favicon_png);
	printf('<link rel="icon" href="%s" type="image/svg+xml">' . "\n", $favicon_svg);
	printf('<link rel="apple-touch-icon" href="%s">' . "\n", $apple_icon);
	printf('<link rel="manifest" href="%s">' . "\n", $manifest);
}
add_action('wp_head', 'ucsc_default_favicons');

/**
 * Tint mobile browser chrome (Android address bar, iOS 15+ tab bar) with UCSC navy.
 */
function ucsc_theme_color_meta()
{
	echo '<meta name="theme-color" content="#00458c">' . "\n";
}
add_action('wp_head', 'ucsc_theme_color_meta');

/**
 * Enqueue theme scripts and styles.
 */
function ucsc_scripts()
{
	wp_enqueue_style(
		'ucsc-styles',
		get_stylesheet_uri(),
		[],
		wp_get_theme()->get('Version')
	);
	$ucsc_css_path = get_theme_file_path('build/style-index.css');
	wp_enqueue_style(
		'ucsc-styles-scss',
		get_template_directory_uri() . '/build/style-index.css',
		[],
		// Version on file contents, not the theme version — otherwise rebuilt
		// assets are served from stale browser caches until the next release.
		file_exists($ucsc_css_path) ? (string) filemtime($ucsc_css_path) : wp_get_theme()->get('Version')
	);
	wp_enqueue_style(
		'ucsc-google-roboto-font',
		'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,600,700,800&display=swap',
		false
	);
	wp_enqueue_style('dashicons');
	$ucsc_theme_asset = file_exists(get_theme_file_path('build/theme.asset.php'))
		? include get_theme_file_path('build/theme.asset.php')
		: ['dependencies' => [], 'version' => wp_get_theme()->get('Version')];
	wp_register_script(
		'ucsc-front',
		get_template_directory_uri() . '/build/theme.js',
		$ucsc_theme_asset['dependencies'],
		$ucsc_theme_asset['version'],
		true
	);
	wp_enqueue_script('ucsc-front');
	wp_enqueue_script(
		'cover-block-video-controls',
		get_theme_file_uri('lib/cover-block-video-controls.js'),
		[],
		wp_get_theme()->get('Version'),
		['strategy' => 'defer']
	);
}
add_action('wp_enqueue_scripts', 'ucsc_scripts');

/**
 * Enqueue additional Google Font Scripts
 */
function ucsc_googleapi_scripts()
{
	echo '<link rel="preconnect" href="https://truss.ucsc.edu">';
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action('wp_head', 'ucsc_googleapi_scripts');

/**
 * Enqueue editor styles and scripts.
 */
function ucsc_add_admin_scripts()
{
	wp_register_script(
		'ucsc-admin-scripts',
		get_template_directory_uri() . '/build/theme.js',
		[],
		wp_get_theme()->get('Version'),
		true
	);
	wp_enqueue_script('ucsc-admin-scripts');
}
add_action('admin_enqueue_scripts', 'ucsc_add_admin_scripts');

/**
 * Set accessible site title tags
 * The core/site-title should be an `h1` on the home page and `p` on all other pages.
 * The core/page-title should be an `h1` on all internal pages.
 *
 * @param  string $block_content 	Block content to be rendered.
 * @param  array  $block         	Block attributes.
 */
function ucsc_modify_site_title($block_content, $block)
{
	// We capture the HTML tag in the site-title block content
	$pattern = '/<(p|h\\d) (.*)<\/(p|h\\d)>/';

	if (is_front_page()) {
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
	remove_filter('render_block_core/site-title', 'ucsc_modify_site_title', 10);
	return $block_content;
}
add_filter('render_block_core/site-title', 'ucsc_modify_site_title', 10, 2);

/**
 * Add author link to Post Author on
 * Single Blog Post
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_post_author_link($block_content = '', $block = [])
{
	// Check for single post; use `global $post` to access data outide the Loop.
	if (is_singular()) {
		if (
			isset($block['blockName']) &&
			'core/post-author-name' === $block['blockName']
		) {
			if (function_exists('coauthors_posts_links')) {
				$block_content = coauthors_posts_links(
					null,
					null,
					null,
					null,
					false
				);
			}
		}
	}
	return $block_content;
}
add_filter('render_block_core/post-author-name', 'ucsc_post_author_link', 8, 2);

/**
 * Add Subtitle meta field
 * Single Blog Post
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_post_subtitle($block_content = '', $block = [])
{
	// Check for single post;
	if (is_single()) {
		$post_id = get_the_id();
		$subtitle = '';
		if (function_exists('get_field')) {
			$subtitle = esc_html(get_field('subtitle-copy'));
		}

		if (
			!empty($subtitle) &&
			isset($block['blockName']) &&
			'core/post-title' === $block['blockName']
		) {
			$html = str_replace(
				$block_content,
				$block_content .
					'<div id="post-subtitle-' .
					$post_id .
					'" class="post-subtitle">
					<p class="post-subtitle-text">' .
					$subtitle .
					'</p></div>',
				$block_content
			);
			return $html;
		}
	}
	return $block_content;
}

add_filter('render_block_core/post-title', 'ucsc_post_subtitle', 10, 2);

/**
 * Find the URL of an uploaded caption file by its file name.
 *
 * Looks the file up in the media library rather than on disk, because the
 * caption may have been uploaded in a different month folder than its video,
 * and some hosts (e.g. CampusPress) serve uploads from a CDN or a rewritten
 * /files/ path that doesn't exist under ABSPATH.
 *
 * Only captions uploaded by someone who can edit the post containing the
 * video are used, so a user who can upload media can't set the captions on
 * a page they can't edit by uploading a .vtt with a matching name.
 *
 * @param string $vtt_name File name to match, e.g. "my-video.vtt".
 * @param int    $post_id  ID of the post the video is shown in, or 0 if the
 *                         video isn't in a post (e.g. a template part).
 * @return string Attachment URL, or an empty string if none was found.
 */
function ucsc_find_vtt_attachment_url($vtt_name, $post_id)
{
	foreach (ucsc_get_vtt_attachment_ids($vtt_name) as $attachment_id) {
		$uploader = (int) get_post_field('post_author', $attachment_id);
		$allowed  = $post_id
			? user_can($uploader, 'edit_post', $post_id)
			: user_can($uploader, 'edit_others_posts');
		if ($allowed) {
			return (string) wp_get_attachment_url($attachment_id);
		}
	}
	return '';
}

/**
 * Get the IDs of caption attachments with a given file name, newest first.
 *
 * @param string $vtt_name File name to match, e.g. "my-video.vtt".
 * @return int[] Attachment IDs.
 */
function ucsc_get_vtt_attachment_ids($vtt_name)
{
	$cache_key = 'ucsc_vtt_ids_' . md5($vtt_name);
	$cached    = get_transient($cache_key);
	if (is_array($cached)) {
		return $cached;
	}

	$attachment_ids = get_posts([
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 20,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => [
			[
				'key'     => '_wp_attached_file',
				'value'   => $vtt_name,
				'compare' => 'LIKE',
			],
		],
	]);

	// LIKE also matches names that only end with $vtt_name (e.g.
	// "other-my-video.vtt"), so require an exact base name match.
	$ids = [];
	foreach ($attachment_ids as $attachment_id) {
		$file = get_post_meta($attachment_id, '_wp_attached_file', true);
		if (wp_basename($file) === $vtt_name) {
			$ids[] = (int) $attachment_id;
		}
	}

	// Matches the max-age CampusPress sends on /files/ upload redirects.
	set_transient($cache_key, $ids, WEEK_IN_SECONDS);
	return $ids;
}

/**
 * Clear the cached caption lookup when a .vtt file is added, changed or removed.
 *
 * @param int $attachment_id Attachment post ID.
 */
function ucsc_clear_vtt_attachment_cache($attachment_id)
{
	$file = get_post_meta($attachment_id, '_wp_attached_file', true);
	if ($file && preg_match('/\.vtt$/i', $file)) {
		delete_transient('ucsc_vtt_ids_' . md5(wp_basename($file)));
	}
}
add_action('add_attachment', 'ucsc_clear_vtt_attachment_cache');
add_action('edit_attachment', 'ucsc_clear_vtt_attachment_cache');
add_action('delete_attachment', 'ucsc_clear_vtt_attachment_cache');

add_filter('render_block_core/cover', function ($block_content, $block, $instance = null) {
	if (empty($block['attrs']['backgroundType']) || $block['attrs']['backgroundType'] !== 'video') {
		return $block_content;
	}
	if (empty($block['attrs']['url'])) {
		return $block_content;
	}

	$path = wp_parse_url($block['attrs']['url'], PHP_URL_PATH);
	if (! $path || ! preg_match('/\.(mp4|webm|mov|m4v)$/i', $path)) {
		return $block_content;
	}
	$vtt_name = preg_replace('/\.(mp4|webm|mov|m4v)$/i', '.vtt', wp_basename($path));

	$post_id = isset($instance->context['postId']) ? (int) $instance->context['postId'] : (int) get_the_ID();
	$vtt_url = ucsc_find_vtt_attachment_url($vtt_name, $post_id);
	if (! $vtt_url) {
		return $block_content;
	}

	// Normalize to the scheme of the current request so the emitted URL
	// isn't blocked as mixed content when the page is served over https.
	$vtt_url = set_url_scheme($vtt_url);

	$track = sprintf(
		'<track kind="captions" src="%s" srclang="en" label="English" default>',
		esc_url($vtt_url)
	);

	// Inject the <track> just before </video>
	return preg_replace('#</video>#', $track . '</video>', $block_content, 1);
}, 10, 3);


/**
 * Breadcrumbs constructor callback helper
 *
 * @return string
 */
function ucsc_breadcrumbs_constructor()
{
	$labels = [
		'title' => '',
	];
	$args = [
		'labels' => $labels,
		'show_on_front' => true,
		'show_trail_end' => true,
		'list_tag'        => 'ol',
		'container_class' => 'ucsc-page-header__breadcrumbs',
	];
	return Hybrid\Breadcrumbs\Trail::render($args);
}



/**
 * Register Image Sizes
 */
if (file_exists(get_theme_file_path('lib/image_sizes.php'))) {
	include get_theme_file_path('lib/image_sizes.php');

	add_action('after_setup_theme', function () {
		$images = new Image_Sizes();
		$images->register_sizes();
		$images->register_size_names();
		$images->get_sizes();
	});
}

/**
 * Enqueue theme block editor style script to modify the "styles" available for blocks in the editor.
 */
function ucsc_block_editor_scripts()
{
	wp_enqueue_script(
		'ucsc-editor',
		get_theme_file_uri('/wp-blocks/styles.js'),
		['wp-blocks', 'wp-dom'],
		wp_get_theme()->get('Version'),
		true
	);
}
add_action('enqueue_block_editor_assets', 'ucsc_block_editor_scripts');

/**
 * Add Truss global header and global footer components
 */
add_action('wp_body_open', 'ucsc_add_custom_body_open_code');

function ucsc_add_custom_body_open_code()
{
	if (function_exists('get_field')) {
		$should_use_logo =
			get_field('toggle_universal_logo_visibility', 'options') ?? 'true';

		// Convert boolean value to string. Trss header accepts only string value
		if (is_bool($should_use_logo)) {
			$should_use_logo = json_encode($should_use_logo);
		}
	}

	echo sprintf(
		'<trss-ucsc-header use-logo="%s" search-action="/" search-query="s" style="--trss-content-width:80rem;"></trss-ucsc-header>',
		$should_use_logo ?? 'true'
	);
}

add_action('wp_footer', 'ucsc_add_custom_body_close_code');

function ucsc_add_custom_body_close_code()
{
	echo '<trss-ucsc-footer style="--trss-content-width:80rem;"></trss-ucsc-footer>';
}

add_action('wp_head', 'ucsc_truss_assets');

function ucsc_truss_assets()
{
?>

	<!-- Script and style to include our components library, Truss.  -->
	<script type="module" src="https://truss.ucsc.edu/ucsc-trss/ucsc-trss.esm.js"></script>
	<link rel="stylesheet" href="https://truss.ucsc.edu/ucsc-trss/ucsc-trss.css">
<?php
}

add_action('wp_footer', 'ucsc_last_modified', 10);

function ucsc_last_modified()
{
	echo '<!-- Last modified code.  --><div class="last-modified__outer" role="region" aria-label="Page last modification date" ><div class="last-modified__inner"><div class="last-modified" >Last modified: ';
	echo do_shortcode('[last-modified]');
	echo '</div></div></div><!-- End last modified code.  -->';
}
/**
 * Add Excerpts to Pages
 */
add_post_type_support('page', 'excerpt');

/**
 * Remove default skip link and add a custom one.
 */
remove_action('wp_footer', 'the_block_template_skip_link');

/**
 * Override the default skip link to pass a11y for landmarks.
 * @return void
 */
function ucsc_skip_link()
{
	/**
	 * Print the skip-link styles.
	 */
?>
	<style id="skip-link-styles">
		.skip-link.screen-reader-text {
			border: 0;
			clip: rect(1px, 1px, 1px, 1px);
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

	<?php /**
	 * Print the skip-link script.
	 */
	?>
	<script>
		(function() {
			var skipLinkTarget = document.querySelector('main'),
				sibling,
				skipLinkTargetID,
				skipLink;

			// Early exit if a skip-link target can't be located.
			if (!skipLinkTarget) {
				return;
			}

			// Get the site wrapper.
			// The skip-link will be injected in the beginning of it.
			sibling = document.body.firstChild;

			// Early exit if the root element was not found.
			if (!sibling) {
				return;
			}

			// Get the skip-link target's ID, and generate one if it doesn't exist.
			skipLinkTargetID = skipLinkTarget.id;
			if (!skipLinkTargetID) {
				skipLinkTargetID = 'wp--skip-link--target';
				skipLinkTarget.id = skipLinkTargetID;
			}

			// Create a block level element to contain the skip link.
			skipLinkContainer = document.createElement('div');
			skipLinkContainer.setAttribute('role', 'navigation');

			// Create the skip link.
			skipLink = document.createElement('a');
			skipLink.setAttribute('aria-label', 'skip to content');
			skipLink.classList.add('skip-link', 'screen-reader-text');
			skipLink.href = '#' + skipLinkTargetID;
			skipLink.innerHTML = '<?php /* translators: Hidden accessibility text. */ esc_html_e('Skip to content', 'ucsc-2022'); ?>';

			skipLinkContainer.append(skipLink);

			// Inject the skip link.
			sibling.parentElement.insertBefore(skipLinkContainer, sibling);
		}());
	</script>
<?php
}

add_action('wp_footer', 'ucsc_skip_link');
