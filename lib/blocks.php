<?php

add_action('init', function () {
	// Register custom blocks from Advanced Custom Fields
	register_block_type(get_template_directory() . '/blocks/main-nav');
	register_block_type(get_template_directory() . '/blocks/breadcrumbs');

	// Remove core block patterns
	remove_theme_support('core-block-patterns');

	// Register Block Categories
	register_block_pattern_category(
		'page_layout',
		array('label' => __('Page Layout', 'ucsc'))
	);

	register_block_pattern_category(
		'text_layout',
		array('label' => __('Text Layout', 'ucsc'))
	);

	register_block_pattern_category(
		'banner',
		array('label' => __('Banner', 'ucsc'))
	);

	register_block_pattern_category(
		'grid',
		array('label' => __('Grid', 'ucsc'))
	);
});
