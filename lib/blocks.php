<?php

add_action( 'init', function() {

	// Register Block Categories
	register_block_pattern_category(
		'ucsc',
		array( 'label' => __( 'UCSC', 'my-plugin' ) )
	);

	register_block_pattern_category(
		'hero',
		array( 'label' => __( 'Hero', 'my-plugin' ) )
	);
} );
