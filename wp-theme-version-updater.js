module.exports.readVersion = function ( contents ) {
	const capturingRegex = /Version: (?<vnum>[0-9]\.[0-9]\.[0-9])/;
	const found = contents.match( capturingRegex );
	return found.groups.vnum;
};

module.exports.writeVersion = function ( _contents, version ) {
	const regex = /Version: (?<vnum>[0-9]\.[0-9]\.[0-9])/;
	return _contents.replace( regex, 'Version: ' + version );
};
