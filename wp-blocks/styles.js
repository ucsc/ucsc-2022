/**
 *  Block styles
 */
wp.domReady(() => {
	// Styles for core/button
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-white",
		label: "White",
		isDefault: true,
		style_handle: "ucsc-button",
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-black",
		label: "Black",
		style_handle: "ucsc-button",
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-blue",
		label: "Blue",
		style_handle: "ucsc-button",
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-red",
		label: "Rubine Red",
		style_handle: "ucsc-button",
	});
	wp.blocks.registerBlockStyle("core/button", {
		name: "ucsc-ocean",
		label: "Ocean",
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

	// Styles for core/image
	wp.blocks.unregisterBlockStyle("core/image", "default");
	wp.blocks.unregisterBlockStyle("core/image", "rounded");
});
