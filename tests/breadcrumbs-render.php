<?php
/**
 * Render smoke check for the core/breadcrumbs template part.
 *
 * Run in the wp-env CLI container with the theme active:
 *   wp eval-file wp-content/themes/ucsc-2022/tests/breadcrumbs-render.php
 */

$failures = [];
$checks = 0;

$assert = static function (bool $ok, string $label) use (&$failures, &$checks): void {
	$checks++;
	if (!$ok) {
		$failures[] = $label;
	}
	WP_CLI::log(($ok ? 'PASS  ' : 'FAIL  ') . $label);
};

// Point the main query and the global post at a fixture, then render the part.
$render_part = static function (array $query_args, ?int $post_id = null, bool $not_found = false): string {
	$query = new WP_Query();
	$query->query($query_args);
	if ($not_found) {
		$query->set_404();
	}
	$GLOBALS['wp_the_query'] = $query;
	$GLOBALS['wp_query'] = $query;
	$GLOBALS['post'] = $post_id ? get_post($post_id) : null;
	if ($post_id) {
		setup_postdata($GLOBALS['post']);
	}
	return do_blocks('<!-- wp:template-part {"slug":"breadcrumbs"} /-->');
};

$original_show_on_front = get_option('show_on_front');
add_filter('deprecated_function_trigger_error', '__return_false');

// Fixtures.
$parent_id = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Crumb parent']);
$child_id = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Crumb child', 'post_parent' => $parent_id]);
$term = wp_insert_term('Crumb category ' . wp_generate_password(6, false), 'category');
$cat_id = $term['term_id'];
$post_id = wp_insert_post(['post_status' => 'publish', 'post_title' => 'Crumb post', 'post_category' => [$cat_id]]);

update_option('show_on_front', 'posts');

// Child page, post and category archive.
$page_html = $render_part(['page_id' => $child_id], $child_id);
$assert(str_contains($page_html, '<nav'), 'child page: renders a nav');
$assert(str_contains($page_html, 'wp-block-breadcrumbs'), 'child page: uses core/breadcrumbs');
$assert(str_contains($page_html, 'aria-current="page"'), 'child page: marks the current item');
$assert(str_contains($page_html, 'Crumb parent'), 'child page: lists the parent page');
$assert(!str_contains($page_html, 'itemprop'), 'child page: prints no microdata');

$post_html = $render_part(['p' => $post_id], $post_id);
$assert(str_contains($post_html, '<nav'), 'post: renders a nav');
$assert(str_contains($post_html, 'aria-current="page"'), 'post: marks the current item');
$assert(!str_contains($post_html, 'itemprop'), 'post: prints no microdata');

$category_html = $render_part(['cat' => $cat_id]);
$assert(str_contains($category_html, '<nav'), 'category: renders a nav');
$assert(str_contains($category_html, 'aria-current="page"'), 'category: marks the current item');
$assert(!str_contains($category_html, 'itemprop'), 'category: prints no microdata');

// Search and 404.
$search_html = $render_part(['s' => 'crumb']);
$assert(str_contains($search_html, '<nav'), 'search: renders a nav');
$assert(str_contains($search_html, 'aria-current="page"'), 'search: marks the current item');
$assert(!str_contains($search_html, 'itemprop'), 'search: prints no microdata');

$not_found_html = $render_part([], null, true);
$assert(str_contains($not_found_html, '<nav'), '404: renders a nav');
$assert(str_contains($not_found_html, 'aria-current="page"'), '404: marks the current item');
$assert(!str_contains($not_found_html, 'itemprop'), '404: prints no microdata');

// Front page: core hides the trail by default.
$front_html = $render_part([]);
$assert(is_front_page(), 'front page: fixture is the front page');
$assert(!str_contains($front_html, '<nav'), 'front page: prints no trail');

// The retired acf/breadcrumbs block and the deprecated function match core.
$render_part(['page_id' => $child_id], $child_id);
$core_nav = do_blocks('<!-- wp:breadcrumbs /-->');
$shim_nav = do_blocks('<!-- wp:acf/breadcrumbs {"name":"acf/breadcrumbs","mode":"preview"} /-->');
$assert('' !== $core_nav, 'shim: core trail is not empty');
$assert($core_nav === $shim_nav, 'shim: acf/breadcrumbs renders the core nav');
$assert($core_nav === ucsc_breadcrumbs_constructor(), 'deprecated function: renders the core nav');

// Clean up.
update_option('show_on_front', $original_show_on_front);
wp_delete_post($post_id, true);
wp_delete_post($child_id, true);
wp_delete_post($parent_id, true);
wp_delete_term($cat_id, 'category');
wp_reset_postdata();

if ($failures) {
	WP_CLI::error(count($failures) . " of $checks checks failed.");
}
WP_CLI::success("All $checks checks passed.");
