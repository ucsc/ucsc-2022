/**
 *  Add three new styles to the editor for buttons:
 * 	Remove default button styles.
 */
wp.domReady(() => {
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
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-red",
		label: "Red",
		style_handle: "ucsc-button",
	});
	wp.blocks.unregisterBlockStyle("core/button", "outline");
	wp.blocks.unregisterBlockStyle("core/button", "fill");
});
