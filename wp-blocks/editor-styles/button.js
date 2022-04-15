/**
 *  Add three new styles to the editor for buttons:
 * 	Remove default button styles.
 */
wp.domReady(() => {
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-blue",
		label: "Blue",
		isDefault: true,
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-gold",
		label: "Gold",
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-red",
		label: "Red",
	});
	wp.blocks.unregisterBlockStyle("core/button", "outline");
	wp.blocks.unregisterBlockStyle("core/button", "fill");
});
