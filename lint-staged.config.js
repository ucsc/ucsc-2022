module.exports = {
	'*.{css,scss}': 'npm run lint:style',
	'*.js': 'npm run format',
	"ignore": ["node_modules", "dist", ".deploy"]
};
