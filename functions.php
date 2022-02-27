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
 * Change site Home Page
 * Main Site vs Subsite
 *
 * @param  string $block_content Block content to be rendered.
 * @param  array  $block         Block attributes.
 * @return string
 */
function ucsc_filter_main_home($block_content = '', $block = [])
{
    if (is_front_page() && is_home() ) {
        // Default homepage
        if (isset($block['blockName']) && 'core/site-title' === $block['blockName']) {
            $ucscURL = "https://www.ucsc.edu";
            $testURL = "localhost:8888";
            $myURL = $_SERVER["REQUEST_URI"];
            if ($ucscURL==$myURL) {
                $html = str_replace(
                    $block_content,
                    '<h1>
				<a href="https://www.ucsc.edu/index.html" class="mainsite-logo" id="logo">UC Santa Cruz</a></h1> ',
                    $block_content
                );
                return $html;
            }
        }
    } elseif (is_front_page()) {
        // Static homepage
        if (isset($block['blockName']) && 'core/post-title' === $block['blockName']) {
            $html = str_replace(
                '<h2 ',
                '<h2 class="hidden" ',
                $block_content
            );
                    return $html;
        }

    } elseif (is_home()) {

        // Blog page
        $html = $block_content;
        return $html;

    } elseif (is_archive()) {

        // Archive page
        $html = $block_content;
        return $html;

    }  else {
        // all other pages
        if (isset($block['blockName']) && 'core/site-title' === $block['blockName']) {
            $html = str_replace(
                '<h1 ',
                '<p ',
                $block_content
            );
                  return $html;
        }
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
add_filter('render_block', 'ucsc_filter_main_home', 10, 2);



/**
 * Utility Function
 */

function jc_utility()
{

    if (is_front_page() && is_home() ) {
        // Default homepage
        echo "<pre>is_front_page() && is_home() = true</pre> Front and Home = Default Home Page";

    } elseif (is_front_page()) {
        // Static homepage
        echo "<pre>is_front_page() = true</pre> Front Page = Static Homepage";

    } elseif (is_home()) {

        // Blog page
        echo "<pre>is_home() = true</pre> home = blog page";

    } elseif (is_archive()) {

        // Archive page: Category, Tag, Etc.
        echo "<pre>is_archive() = true</pre> archive page";

    } else {

        // Everything else
        echo "all other pages";

    }

}
// add_action('wp_head', 'jc_utility');

