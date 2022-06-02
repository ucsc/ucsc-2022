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

 *  Remove core block patterns
 */
add_action(
	'init',
	function() {
		remove_theme_support( 'core-block-patterns' );
	}
);

/**
 * Register block pattern category 'UCSC Patterns'
 *
 * @return void
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'ucsc',
		array( 'label' => __( 'UCSC Patterns', 'ucsc-2022' ) )
	);

}

if ( function_exists( 'register_block_pattern' ) ) {

	/**
	 * Register block patterns for UCSC webites
	 *
	 * @return void
	 */
	function ucsc_2022_block_patterns() {

		register_block_pattern(
			'ucsc-2022/hero',
			array(
				'title'       => __( 'Hero with button', 'ucsc-2022' ),
				'description' => _x( 'A header area for the top of a home page that has an image, headline, teaser, and call-to-action button.', 'Block pattern description', 'ucsc-2022' ),
				'categories'  => array( 'hero', 'ucsc' ),
				'content'     => "<!-- wp:cover {\"url\":\"https://unsplash.com/photos/ylJtKpTYjn4/download?ixid=MnwxMjA3fDB8MXxzZWFyY2h8MXx8cmVkd29vZCUyMGZvcmVzdHxlbnwwfHx8fDE2NTI2Mzg2MTM&w=1920\",\"dimRatio\":60,\"overlayColor\":\"ucsc-primary-blue\",\"focalPoint\":{\"x\":\"0.19\",\"y\":\"0.82\"},\"minHeight\":704,\"minHeightUnit\":\"px\",\"contentPosition\":\"bottom center\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"80px\",\"bottom\":\"80px\"}}}} -->\n<div class=\"wp-block-cover has-custom-content-position is-position-bottom-center\" style=\"padding-top:80px;padding-bottom:80px;min-height:704px\"><span aria-hidden=\"true\" class=\"wp-block-cover__background has-ucsc-primary-blue-background-color has-background-dim-60 has-background-dim\"></span><img class=\"wp-block-cover__image-background\" alt=\"A redwood forest.\" src=\"https://unsplash.com/photos/ylJtKpTYjn4/download?ixid=MnwxMjA3fDB8MXxzZWFyY2h8MXx8cmVkd29vZCUyMGZvcmVzdHxlbnwwfHx8fDE2NTI2Mzg2MTM&w=1920\" style=\"object-position:19% 82%\" data-object-fit=\"cover\" data-object-position=\"19% 82%\"/><div class=\"wp-block-cover__inner-container\"><!-- wp:group {\"layout\":{\"inherit\":true}} -->\n<div class=\"wp-block-group\"><!-- wp:columns {\"verticalAlignment\":\"bottom\"} -->\n<div class=\"wp-block-columns are-vertically-aligned-bottom\"><!-- wp:column {\"verticalAlignment\":\"bottom\"} -->\n<div class=\"wp-block-column is-vertically-aligned-bottom\"><!-- wp:paragraph {\"style\":{\"typography\":{\"textTransform\":\"uppercase\",\"letterSpacing\":\"4px\"}},\"textColor\":\"white\",\"fontSize\":\"small\"} -->\n<p class=\"has-white-color has-text-color has-small-font-size\" style=\"text-transform:uppercase;letter-spacing:4px\">UC Santa Cruz</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading {\"style\":{\"typography\":{\"fontStyle\":\"normal\",\"fontWeight\":\"900\",\"textTransform\":\"capitalize\"},\"spacing\":{\"margin\":{\"bottom\":\"0px\"}}},\"fontSize\":\"xxx-large\"} -->\n<h2 class=\"has-xxx-large-font-size\" style=\"font-style:normal;font-weight:900;margin-bottom:0px;text-transform:capitalize\">The Perfect Environment For You</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"fontSize\":\"base\"} -->\n<p class=\"has-base-font-size\">Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top-line web services vis-a-vis cutting-edge deliverables.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"left\",\"orientation\":\"horizontal\"},\"style\":{\"spacing\":{\"blockGap\":{\"top\":\"15px\",\"left\":\"15px\"}}}} -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"className\":\"is-style-ucsc-red\",\"fontSize\":\"base\",\"fontFamily\":\"roboto\"} -->\n<div class=\"wp-block-button has-custom-font-size is-style-ucsc-red has-roboto-font-family has-base-font-size\"><a class=\"wp-block-button__link\">Say YES to UCSC</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div>\n<!-- /wp:group --></div></div>\n<!-- /wp:cover -->",
			)
		);
	}

		add_action( 'init', 'ucsc_2022_block_patterns' );

}
