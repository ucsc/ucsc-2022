<?php

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support for post thumbnails.
 */

if (!function_exists('ucsc_theme_setup')) :

    function ucsc_theme_setup()
    {

        add_theme_support('wp-block-styles');

        add_editor_style('build/style-index.css');

        /**
         * Register primary navigation menu location
         */
        register_nav_menus(
            array(
            'primary'   => __('Primary Navigation', 'theme-ucsc'),
            )
        );
    }
endif;
add_action('after_setup_theme', 'ucsc_theme_setup');

/**
 * Enqueue theme scripts and styles.
 */
function ucsc_theme_scripts()
{
    wp_enqueue_style('ucsc-theme-styles', get_stylesheet_uri());
    wp_enqueue_style('ucsc-theme-styles-scss', get_template_directory_uri() . '/build/style-index.css');
}
add_action('wp_enqueue_scripts', 'ucsc_theme_scripts');

/**
 * Enqueue additional Google Font Scripts
 */
function ucsc_googleapi_scripts()
{
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action('wp_head', 'ucsc_googleapi_scripts');

/**
 * Enqueue editor styles and scripts.
 */

function ucsc_add_admin_scripts()
{
    wp_register_script('ucsc-admin-scripts', get_template_directory_uri() . '/build/theme.js', array(), '', true);
    wp_enqueue_script('ucsc-admin-scripts');
}

add_action('admin_enqueue_scripts', 'ucsc_add_admin_scripts');

/**
 * Enqueue fonts, styles and scripts.
 */

function ucsc_add_scripts()
{
    wp_enqueue_style('ucsc-google-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto:100,300,400,500,700&display=swap', false);
    wp_register_script('ucsc-front', get_template_directory_uri() . '/build/theme.js', array(), '', true);
    wp_enqueue_script('ucsc-front');
}

add_action('wp_enqueue_scripts', 'ucsc_add_scripts');


/**
 * Copyright shortcode
 * returns copyright symbol and current year
 */
function ucsc_copyright()
{
    $copyright = '&#169;';
    $year = date('Y');
    return $copyright . $year;
}
add_shortcode('copyright', 'ucsc_copyright');

/**
 * Return the last page modification in a readable format
 *
 * This is called by a short code `last-modified`. It looks at the
 * modified time and if that time is greater than zero it returns it
 * as a formatted date. Otherwise, it returns the date the page was created.
 *
 * @return String
 */
function ucsc_last_modified_helper()
{
    $ucsc_modified_time = get_the_modified_time('U');
    if ($ucsc_modified_time > 0) {
        return the_modified_time('F jS, Y');
    } else {
        return the_time('F jS, Y');
    }
}

function ucsc_last_modified()
{
    ob_start();
    ucsc_last_modified_helper();
    return ob_get_clean();
}
add_shortcode('last-modified', 'ucsc_last_modified');

/**
 * Change site title block content
 * on Mainsite Home Page Template
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_filter_mainsite_home_site_title($block_content = '', $block = [])
{
    if (is_page_template('front-page-mainsite')) {
        if (isset($block['blockName']) && 'core/site-title' === $block['blockName']) {
            $html = str_replace(
                $block_content,
                '<h1>
				<a href="https://www.ucsc.edu/index.html" class="mainsite-logo" id="logo">UC Santa Cruz</a></h1> ',
                $block_content
            );
            return $html;
        }
    }
    return $block_content;
}
// add_filter('render_block', 'ucsc_filter_mainsite_home_site_title', 10, 2);

/**
 * Change site title block element from H1 to P
 * on Page Templates
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_filter_page_site_title($block_content = '', $block = [])
{
    if (!is_front_page()) {
        if (isset($block['blockName']) && 'core/site-title' === $block['blockName']) {
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
add_filter('render_block', 'ucsc_filter_page_site_title', 10, 2);

/**
 * Change Page title block element from H2 to H1
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_filter_single_page_title($block_content = '', $block = [])
{
    if (is_page()) {
        if (isset($block['blockName']) && 'core/post-title' === $block['blockName']) {
            $html = str_replace(
                '<h2 ',
                '<h1 ',
                $block_content
            );
            return $html;
        }
    }
    return $block_content;
}
add_filter('render_block', 'ucsc_filter_single_page_title', 10, 2);

/**
 * Utility Function
 */

function jc_test()
{
    if (!$post) {
        global $post;
    }
    if (!$post) {
        return '';
    }
    // $blocks = parse_blocks($post->post_content);

    // echo ($blocks[0]['innerHtml']);
    $template = get_page_template_slug(get_queried_object_id());
    $front = is_front_page();
    $home = is_home();
    echo var_dump($template);
	echo var_dump($home);
 	// print_r($template);

    // print_r($template);
}

// add_action('wp_head', 'jc_test');

function jc_get_title($post = false)
{
    if (!$post) {
        global $post;
    }
    if (!$post) {
        return '';
    }
    $postTitle = '';
    $blocks = parse_blocks($post->post_content);
    if (count($blocks) == 1 && $blocks[0]['blockName'] == null) {  // Non-Gutenberg posts
        $postTitle = get_the_title($post->ID);
    } else {
        foreach ($blocks as $block) {
            if ($block['blockName'] == 'core/post-title') {
                // $postTitle = strip_tags($block['innerHTML']);
                $postTitle = $block['innerHTML'];
            }
        }
    }
    // return "<div class='excerpt'>$excerpt</div>";
    // return $postTitle;
    //var_dump($postTitle);
    // print_r($postTitle);
}
