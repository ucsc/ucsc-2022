/**
 * External Dependencies
 */
const path = require('path');
/**
 * WordPress Dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

console.log();

module.exports = {
	...defaultConfig,
	...{
		entry: {
			index: path.resolve(process.cwd(), 'src', 'index.js'),
			theme: path.resolve(process.cwd(), 'src', 'theme.js'),
		},
	},
};
