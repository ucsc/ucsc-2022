<?php declare(strict_types=1);

add_action('init', static function (): void {
	// Register custom blocks from Advanced Custom Fields
	register_block_type(get_template_directory() . '/blocks/main-nav');
	register_block_type(get_template_directory() . '/blocks/breadcrumbs');

	// Remove core block patterns
	remove_theme_support('core-block-patterns');

	// Remove included patterns that line 8 doesn't remove
	if ( has_block( 'core/social-links-shared-background-color' ) ) {
		unregister_block_pattern( 'core/social-links-shared-background-color' );
	}
	if ( has_block( 'core/query-large-title-posts ' ) ) {
		unregister_block_pattern( 'core/query-large-title-posts' );
	}

	// Register Block Categories
	register_block_pattern_category(
		'page_layout',
		['label' => __('Page Layout', 'ucsc')]
	);

	register_block_pattern_category(
		'text_layout',
		['label' => __('Text Layout', 'ucsc')]
	);

	register_block_pattern_category(
		'banner',
		['label' => __('Banner', 'ucsc')]
	);

	register_block_pattern_category(
		'examples',
		array('label' => __('Page Examples', 'ucsc'))
	);

	register_block_pattern_category(
		'grid',
		['label' => __('Grid', 'ucsc')]
	);
});
