<?php
/**
 * UCSC Block Patterns and Block Pattern Categories
 *
 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/
 *
 * @package UCSC
 * @since UCSC 1.0.0
 */

/**
 *  Removes core block patterns
 *
 */
add_action(
	'init',
	function() {
		remove_theme_support( 'core-block-patterns' );
	}
);

/**
 * Registers block patterns and categories.
 *
 * @return void
 */
function ucsc_register_block_patterns() {
	$block_pattern_categories = array(
		'featured' => array( 'label' => __( 'Featured', 'ucsc-2022' ) ),
		'footer'   => array( 'label' => __( 'Footers', 'ucsc-2022' ) ),
		'header'   => array( 'label' => __( 'Headers', 'ucsc-2022' ) ),
		'query'    => array( 'label' => __( 'Query', 'ucsc-2022' ) ),
		'pages'    => array( 'label' => __( 'Pages', 'ucsc-2022' ) ),
	);

	/**
	 * Filters the theme block pattern categories.
	 *
	 * @param array[] $block_pattern_categories {
	 *     An associative array of block pattern categories, keyed by category name.
	 *
	 *     @type array[] $properties {
	 *         An array of block category properties.
	 *
	 *         @type string $label A human-readable label for the pattern category.
	 *     }
	 * }
	 */
	$block_pattern_categories = apply_filters( 'ucsc_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}

	$block_patterns = array(
		'hero-and-text',
	);

	/**
	 * Filters the theme block patterns.
	 *
	 * @param array $block_patterns List of block patterns by name.
	 */
	$block_patterns = apply_filters( 'ucsc_block_patterns', $block_patterns );

	foreach ( $block_patterns as $block_pattern ) {
		$pattern_file = get_theme_file_path( '/lib/patterns/' . $block_pattern . '.php' );

		register_block_pattern(
			'ucsc-2022/' . $block_pattern,
			require $pattern_file
		);
	}
}
add_action( 'init', 'ucsc_register_block_patterns', 9 );
