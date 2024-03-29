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
$ucsc_header                    = do_blocks( '<!-- wp:template-part {"slug":"ucsc-header","theme":"ucsc-2022","className":"ucsc-header"} /-->' . '<!-- wp:template-part {"slug":"site-header","theme":"ucsc-2022","className":"site-header"} /-->' );
$GLOBALS['ucsc_plugin_content'] = do_blocks( '<!-- wp:template-part {"className":"plugin-content","slug":"content-plugin","theme":"ucsc-2022"} /-->' );
$GLOBALS['ucsc_footer']         = do_blocks( '<!-- wp:template-part {"slug":"site-footer","theme":"ucsc-2022","className":"site-footer"} /-->' . '<!-- wp:template-part {"slug":"ucsc-footer","theme":"ucsc-2022","className":"ucsc-footer"} /-->' );
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
