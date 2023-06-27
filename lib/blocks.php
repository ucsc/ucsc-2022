<?php

add_action( 'init', function () {
	// Register Main Nav Block
	register_block_type( get_template_directory() . '/blocks/main-nav' );

	// Register Block Categories
	register_block_pattern_category(
		'ucsc',
		array( 'label' => __( 'UCSC', 'ucsc' ) )
	);

	register_block_pattern_category(
		'hero',
		array( 'label' => __( 'Hero', 'ucsc' ) )
	);

	register_block_pattern_category(
		'cards',
		array( 'label' => __( 'Cards', 'ucsc' ) )
	);
} );
