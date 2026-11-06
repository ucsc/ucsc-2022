// wordpress-version-updater.js
try {
	module.exports = require( '@ucsantacruz/wordpress-version-updater' );
} catch ( err ) {
	console.error(
		err.code === 'MODULE_NOT_FOUND'
			? 'Missing @ucsantacruz/wordpress-version-updater. Run `npm ci` before releasing.'
			: `Could not load @ucsantacruz/wordpress-version-updater: ${ err.message }`
	);
	process.exit( 1 );
}
