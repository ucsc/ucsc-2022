const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');

const root = path.resolve(__dirname, '..');
const updaterFile = 'wordpress-version-updater.js';
const pkg = require(path.join(root, 'package.json'));

test('style.css is the first bump file and uses the local updater', () => {
	const [first] = pkg['commit-and-tag-version'].bumpFiles;

	assert.deepEqual(first, { filename: 'style.css', updater: updaterFile });
});

test('the local updater reads the package version from style.css', () => {
	const updater = require(path.join(root, updaterFile));
	const css = fs.readFileSync(path.join(root, 'style.css'), 'utf8');

	assert.equal(updater.readVersion(css), pkg.version);
});

test('the local updater exits with an error if the package is missing', () => {
	const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'version-updater-'));
	const copy = path.join(dir, updaterFile);

	fs.copyFileSync(path.join(root, updaterFile), copy);

	const result = spawnSync(process.execPath, [copy], { encoding: 'utf8' });

	fs.rmSync(dir, { recursive: true });

	assert.equal(result.status, 1);
	assert.match(result.stderr, /Run `npm ci` before releasing/);
});
