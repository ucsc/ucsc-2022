<?php
/**
 * Enqueue custom blocks
 */

// Define constants.
define( 'ACF_DIR', dirname( __FILE__ ) );
define( 'TEMPLATE_DIR_URI', get_template_directory_uri() );

// Include Customization files.

// add_action( 'acf/init', 'ucsc_acf_init_block_types' );
function ucsc_acf_init_block_types() {

	if ( function_exists( 'acf_register_block_type' ) ) {

		// register a post subtitle block.
		acf_register_block_type(
			array(
				'name'            => 'subtitle',
				'title'           => __( 'Subtitle' ),
				'description'     => __( 'A custom post subtitle block.' ),
				'align_text'      => 'right',
				'render_template' => ACF_DIR . '/block-templates/post-subtitle.php',
				'enqueue_style'   => TEMPLATE_DIR_URI . '/acf-blocks/block-styles/post-subtitle.css',
				'category'        => 'formatting',
				'supports'        => array(
					'align_text' => true,
				),
				'icon'            => 'text',
				'keywords'        => array( 'subtitle' ),
			)
		);
	}
	if ( function_exists( 'acf_register_block_type' ) ) {

		// Register Campus Message block.
		acf_register_block_type(
			array(
				'name'            => 'campus-message',
				'title'           => __( 'Campus Message' ),
				'description'     => __( 'Custom block for displaying campus messages header.' ),
				'align_text'      => 'left',
				'render_template' => ACF_DIR . '/block-templates/campus-message.php',
				'enqueue_style'   => TEMPLATE_DIR_URI . '/acf-blocks/block-styles/campus-message.css',
				'category'        => 'formatting',
				'supports'        => array(
					'align_text' => true,
				),
				'icon'            => 'text',
				'keywords'        => array( 'message', 'dean' ),
			)
		);
	}
}
