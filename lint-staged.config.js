module.exports = {
	'*.{css,scss}': 'npm run prettier:write',
	'*.js': 'npm run format',
	'*.php': 'composer lint-fix',
};
