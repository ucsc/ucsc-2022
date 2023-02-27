/**
 *  Block styles and variations and variations
 */
wp.domReady(() => {
	// Styles for core/group
	wp.blocks.registerBlockStyle('core/group', {
		name: 'ucsc-waveform',
		label: 'Waveform',
		style_handle: 'ucsc-group',
	});
	// Styles for core/list
	wp.blocks.registerBlockStyle('core/list', {
		name: 'ucsc-side-navigation',
		label: 'Side Navigation',
		style_handle: 'ucsc-list',
	});
	wp.blocks.registerBlockStyle('core/list', {
		name: 'ucsc-upper-alpha-list',
		label: 'Upper alpha',
		style_handle: 'ucsc-list',
	});
	wp.blocks.registerBlockStyle('core/list', {
		name: 'ucsc-lower-alpha-list',
		label: 'Lower alpha',
		style_handle: 'ucsc-list',
	});
	// Styles for core/button
	wp.blocks.registerBlockStyle('core/button', {
		name: 'ucsc-blue',
		label: 'Blue',
		style_handle: 'ucsc-button',
	});
	wp.blocks.registerBlockStyle('core/button', {
		name: 'ucsc-black',
		label: 'Black',
		style_handle: 'ucsc-button',
	});
	wp.blocks.registerBlockStyle('core/button', {
		name: 'ucsc-gold',
		label: 'Gold',
		style_handle: 'ucsc-button',
	});
	wp.blocks.registerBlockStyle('core/button', {
		name: 'ucsc-ocean',
		label: 'Ocean',
		style_handle: 'ucsc-button',
	});
	wp.blocks.unregisterBlockStyle('core/button', 'outline');
	wp.blocks.unregisterBlockStyle('core/button', 'fill');

	// Styles for core/separator
	wp.blocks.unregisterBlockStyle('core/separator', 'default');
	wp.blocks.unregisterBlockStyle('core/separator', 'dots');
	wp.blocks.unregisterBlockStyle('core/separator', 'wide');

	// Styles for core/quote
	wp.blocks.unregisterBlockStyle('core/quote', 'default');
	wp.blocks.unregisterBlockStyle('core/quote', 'plain');

	// Styles for core/image
	wp.blocks.unregisterBlockStyle('core/image', 'default');
	wp.blocks.unregisterBlockStyle('core/image', 'rounded');

	// Styles for core/rss
	wp.blocks.registerBlockStyle('core/rss', {
		name: 'ucsc-rss-block',
		label: 'Home Block',
		style_handle: 'ucsc-rss',
	});
	wp.blocks.registerBlockStyle('core/rss', {
		name: 'ucsc-rss-list',
		label: 'Simple List',
		isDefault: true,
		style_handle: 'ucsc-rss',
	});

	// Variation for core/spacer
	wp.blocks.registerBlockVariation('core/spacer', {
		isDefault: true,
		name: 'spacer',
		title: 'Spacer',
		attributes: {
			height: '4rem',
		},
	});
});
