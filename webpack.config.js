/**
 * External Dependencies
 */
const path = require( 'path' );
/**
 * WordPress Dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
    ...defaultConfig,
    ...{
        entry: {
            index: path.resolve( process.cwd(), 'src', 'index.js' ),
            "front": path.resolve( process.cwd(), 'src', 'front.js' ),
            // "beer-list": path.resolve( process.cwd(), 'src', 'beer-list.css' ),
        },
    }
}
