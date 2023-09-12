# Official WordPress theme for UC Santa Cruz

This theme is being developed in the open. You can preview the work in progress here: <https://www-next.ucsc.edu>

## Theme requirements

- WordPress 6.2
- This theme **requires** the following plugins:
  - [UCSC Custom Functionality](https://github.com/ucsc/ucsc-custom-functionality), which adds analytics code and custom shortcodes
  - [Advanced Custom Fields PRO](https://advancedcustomfields.com)
  - [The Icon Block](https://nickdiego.com/projects/icon-block/) by Nick Diego

## Development requirements

- Node and NPM
- [Composer](https://getcomposer.org/) and PHP >8.0
- [Local by WP Engine](https://localwp.com/) for the development server

## Development workflow

1. Create a local development site with [Local by WP Engine](https://localwp.com/)
2. Clone this respository into your site's `themes` directory
3. `cd` into the project repo directory
4. Install project dependencies with `npm install`, then `composer install`
5. Compile required stylesheets with `npm run build`

### For testing

- Install the [WordPress Theme Check](https://wordpress.org/plugins/theme-check/) to test the theme
- Use [FakerPress](https://wordpress.org/plugins/fakerpress/) to generate sample content for testing

## Contributors

The UCSC WordPress theme is maintained by the University Relations web team in the campus [Communications & Marketing](https://communications.ucsc.edu) office. If you have any questions about this project, you can contact [Rob Knight](https://campusdirectory.ucsc.edu/cd_detail?uid=raknight), [Jason Chafin](https://campusdirectory.ucsc.edu/cd_detail?uid=jchafin), or [submit an issue](https://github.com/ucsc/theme-ucsc/issues) here on Github.
