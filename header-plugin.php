<?php
/**
 * The Dynamic Content Header.
 *
 * Includes the header down to the opening <main>.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage ucsc-2022
 * @since 1.0.0
 */

/** See: https://github.com/WordPress/gutenberg/issues/39207#issuecomment-1087347901 */
$ucsc_header = do_blocks( '<!-- wp:template-part {"slug":"header","theme":"ucsc-2022","tagName":"header","className":"site-header"} /-->' );
$GLOBALS['ucsc_plugin_content'] = do_blocks( '<!-- wp:template-part {"className":"plugin-content","slug":"content-plugin","theme":"ucsc-2022"} /-->' );
$GLOBALS['ucsc_footer'] = do_blocks( '<!-- wp:template-part {"slug":"footer","theme":"ucsc-2022","tagName":"footer","className":"site-footer"} /-->' );
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open();
echo $ucsc_header;
?>
