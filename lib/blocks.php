<?php

add_action( 'init', function () {
	// Register Main Nav Block
	register_block_type( get_template_directory() . '/blocks/main-nav' );

 	// Remove core block patterns
	remove_theme_support( 'core-block-patterns' );

	// Remove included patterns that line 8 doesn't remove
	unregister_block_pattern( 'core/social-links-shared-background-color' );
	unregister_block_pattern( 'core/query-large-title-posts' );

	// Register Block Categories
	register_block_pattern_category(
		'page_layout',
		array( 'label' => __( 'Page Layout', 'ucsc' ) )
	);

	register_block_pattern_category(
		'text_layout',
		array( 'label' => __( 'Text Layout', 'ucsc' ) )
	);

	register_block_pattern_category(
		'banner',
		array( 'label' => __( 'Banner', 'ucsc' ) )
	);

	register_block_pattern_category(
		'grid',
		array( 'label' => __( 'Grid', 'ucsc' ) )
	);
} );
