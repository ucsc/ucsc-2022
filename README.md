# Official WordPress theme for UC Santa Cruz

A theme demo with official WordPress theme unit test data is here: <https://dev-ucsc-theme-demo.pantheonsite.io/>

## Requirements (as of 2022-04-15)

- WordPress 5.8 with the [Gutenberg plugin](https://wordpress.org/plugins/gutenberg/) installed, or WordPress 5.9.
- [Docker](https://www.docker.com/) and [@WordPress/env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) installed.
- [Composer](https://getcomposer.org/) and PHP 7.4
- We also recommend the [CampusPress theme and plugin](https://github.com/igmoweb/theme-check) check as well.

## Development workflow

Our workflow uses [Docker](https://www.docker.com/) and [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/). This is an easy way to run a local WordPress development environment. 🎉 If you are getting started with wp-env, create a file named, follow these steps to get started.

1. Create a project folder, and clone this repository into it (see the file structure below)
2. Create a file named `.wp-env.json` in your project folder and copy the code [from this page](https://github.com/ucsc/theme-ucsc/wiki/Example-.wp-env.json-file) into that file.
3. Add any additional plugins and themes into the `wp-plugins` and the `wp-themes` folders then follow the instructions below. Your project directory should look like this:

```text
project/
	|---CLONE_THIS_REPO_HERE
    |---.wp-env.json
    |---wp-plugins/
	|---mu-plugins/
    |---wp-themes/
```

1. In the folder where you cloned this theme, run `npm install && npm run build`, followed by `composer install` to compile theme files and install dependencies.
2. `cd` to project root and run `wp-env start`
3. Login (`admin:password`) and navigate to <http://localhost:8888/wp-admin/import.php>
4. At this point you can create content on your own, or import the [theme unit test data](https://codex.wordpress.org/Theme_Unit_Test) WordPress theme developers use.
5. To reset your development environment (delete all content, update WordPress core), run `wp-env destroy`, then `wp-env start`.

## Contributors

The UCSC WordPress theme is maintained by the Digital Strategies team in the campus [Communications & Marketing](https://communications.ucsc.edu) office. If you have any questions about this project, you can contact [Rob Knight](https://campusdirectory.ucsc.edu/cd_detail?uid=raknight), [Jason Chafin](https://campusdirectory.ucsc.edu/cd_detail?uid=jchafin), or [submit an issue](https://github.com/ucsc/theme-ucsc/issues) here on Github.
