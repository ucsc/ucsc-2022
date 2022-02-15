# Official WordPress theme for UC Santa Cruz

A theme demo with official WordPress theme unit test data is here: <https://dev-ucsc-theme-demo.pantheonsite.io/>

## Requirements (as of 2022-02-04)

- Requires WordPress 5.9 or above
- We recommend the [CampusPress theme and plugin](https://github.com/igmoweb/theme-check) check as well.

## Development workflow

This workflow assumes you have [Docker](https://www.docker.com/) running and [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) installed. This is by far the easiest way to run a local WordPress development environment. 🎉 If you are just getting started with wp-env, we provide a sample wp-env.json file in this respository. Rename that file from `example.wp-env.json` to `.wp-env.json` and move it to the root of your project directory, then follow the instructions below. Your project directory should look like this:

```text
project/
    |---.wp-env.json
    |---plugins/
	|---mu-plugins/
    |---themes/
		|---[clone UCSC theme repo here]
```

1. In your themes subfolder, `gh repo clone ucsc/theme-ucsc ucsc`.
2. In the `ucsc` theme folder, run `npm install && npm run build` to compile theme files.
3. `cd` to root folder and run `wp-env start`
4. Login (`admin:password`) and navigate to <http://localhost:8888/wp-admin/import.php>
5. At this point you can create content on your own, or import the [theme unit test data](https://codex.wordpress.org/Theme_Unit_Test) WordPress theme developers use.
6. To reset your development environment (delete all content, update WordPress core), run `wp-env destroy`, then `wp-env start`.
