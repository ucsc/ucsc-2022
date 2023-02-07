/**
 * External Dependencies
 */
const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
/**
 * WordPress Dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

module.exports = {
	...defaultConfig,
	...{
		entry: {
			index: path.resolve(process.cwd(), 'src', 'index.js'),
			theme: path.resolve(process.cwd(), 'src', 'theme.js'),
			truss: path.resolve(process.cwd(), 'src', 'truss.js'),
		},
	},
};
