<?php

if (! function_exists('ucsc_theme_setup') ) :

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

        /*
        * Load additional block styles.
        */
        $styled_blocks = ['button'];
        foreach ($styled_blocks as $block_name) {
            $args = array(
             'handle' => "ucsc-$block_name",
             'src'    => get_theme_file_uri("src/scss/wp-blocks/$block_name.css"),
             $args['path'] = get_theme_file_path("src/scss/wp-blocks/$block_name.css"),
            );
            wp_enqueue_block_style("core/$block_name", $args);
        }
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
 * Change Site Title and Logo
 * Main Site vs Subsite
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_logo_switch($block_content = '', $block = [])
{
    $siteURL = get_site_url();
    // http://localhost:8888 == www
    if ($siteURL == '') {
        if (isset($block['blockName']) && 'core/html' === $block['blockName']) {
            $html = '';
            return $html;
        } else if ((is_front_page() && is_home()) || is_front_page() || is_home()) {
            if (isset($block['blockName']) && 'core/site-title' === $block['blockName']) {
                $html = str_replace(
                    $block_content,
                    '<h1>
						<a href="https://www.ucsc.edu" class="mainsite-logo" id="logo">UC Santa Cruz</a></h1> ',
                    $block_content
                );
                return $html;
            }
        } else {
            if (isset($block['blockName']) && 'core/site-title' === $block['blockName']) {
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


add_filter('render_block', 'ucsc_logo_switch', 10, 2);

/**
 * Set accessible HTML headers
 * The site title is the `h1` on the home page and `p` on all other pages.
 * The page title is the `h1` on all other pages.
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_adjust_structure($block_content = '', $block = [])
{
    if (is_front_page()) {
        // On the home page, return the block as is
        $html = $block_content;
        return $html;
    } else {
        // On all other pages, the site title becomes `p` and page title becomes `h1`
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
add_filter('render_block', 'ucsc_adjust_structure', 10, 2);

/**
 * Add author link to Post Author on
 * Single Blog Post
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_post_author_link($block_content = '', $block = [])
{
		// Check for single post; use `global $post` to access data outide the Loop
    if (is_single()) {
				global $post;
				$author_id = $post->post_author;
				$author_nicename = get_the_author_meta('nicename', $author_id);
				$author_email = get_the_author_meta('user_email', $author_id);
        if (isset($block['blockName']) && 'core/post-author' === $block['blockName']) {
            $html = str_replace($block_content,'<p class="wp-block-post-author__name"><a href="mailto:'.$author_email.'">'.$author_nicename.'</a></p>',$block_content);
                return $html;
        }
    }
    return $block_content;
}
add_filter('render_block', 'ucsc_post_author_link', 10, 2);

/**Utility Function
 * TODO: Remove when done
*/

function wpdocs_display_post_youtube_block() {

    global $post;

    $blocks = parse_blocks( $post->post_content );

    foreach ( $blocks as $block ) {



            echo apply_filters( 'the_content', render_block( $block ) );

            break;



    }

}
// wpdocs_display_post_youtube_block();
// jc_test();
// add_action('wp_head','wpdocs_display_post_youtube_block');

function this_function() {
echo "Hello World";
}

// this_function();
