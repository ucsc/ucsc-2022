/**
 *  Add three new styles to the editor for buttons:
 * 	Remove default button styles.
 */
wp.domReady(() => {
	// Styles for buttons
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

	// Styles for separators
	wp.blocks.registerBlockStyle("core/separator", {
		name: "ucsc-small",
		label: "Small",
		isDefault: true,
		style_handle: "ucsc-separator",
	});
	wp.blocks.registerBlockStyle("core/separator", {
		name: "ucsc-medium",
		label: "Medium",
		style_handle: "ucsc-separator",
	});
	wp.blocks.registerBlockStyle("core/separator", {
		name: "ucsc-large",
		label: "Large",
		style_handle: "ucsc-separator",
	});
	wp.blocks.unregisterBlockStyle("core/separator", "default");
	wp.blocks.unregisterBlockStyle("core/separator", "dots");
});
