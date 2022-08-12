/**
 *  Block styles
 */
wp.domReady(() => {
	// Styles for core/button
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-blue",
		label: "Blue",
		isDefault: true,
		style_handle: "ucsc-button",
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-gold",
		label: "Gold",
		style_handle: "ucsc-button",
	});
	wp.blocks.unregisterBlockStyle("core/button", "outline");
	wp.blocks.unregisterBlockStyle("core/button", "fill");

	// Styles for core/separator
	wp.blocks.unregisterBlockStyle("core/separator", "default");
	wp.blocks.unregisterBlockStyle("core/separator", "dots");
	wp.blocks.unregisterBlockStyle("core/separator", "wide");

	// Styles for core/quote
	wp.blocks.unregisterBlockStyle("core/quote", "default");
	wp.blocks.unregisterBlockStyle("core/quote", "plain");
});
