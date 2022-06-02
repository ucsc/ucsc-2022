/**
 * External Dependencies
 */
const path = require( 'path' );
/**
 * WordPress Dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

/**
 * Based on this answer from Stack Overflow
 * url: https://stackoverflow.com/questions/35903246/how-to-create-multiple-output-paths-in-webpack-config
 */

var config = {
	// TODO: Add common Configuration
	module: {},
};

var mainConfig = Object.assign({}, config, {
	...defaultConfig,
	...{
		entry: {
			index: path.resolve(process.cwd(), 'src', 'index.js'),
			"theme": path.resolve(process.cwd(), 'src', 'theme.js'),
		},
		output: {
			path: path.resolve(process.cwd(), 'build'),

		}
	},
});

var pluginConfig = Object.assign({}, config, {
	...defaultConfig,
	...{
		name: "plugin",
		entry: path.resolve(__dirname, 'src/plugin-content/plugin.scss'),
		output: {
			path: path.resolve(__dirname, 'build/plugin-content'),
			filename: "plugin-content.js",

		},
	}
});

// Return Array of Configurations
module.exports = [
	mainConfig,
];

