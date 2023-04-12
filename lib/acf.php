<?php

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_6245e37f66491',
			'title'                 => 'Subtitle',
			'fields'                => array(
				array(
					'key'               => 'field_6245e37f6a110',
					'label'             => 'Text for subtitle',
					'name'              => 'subtitle-copy',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => 'Add post or page subtitle',
			'show_in_rest'          => 1,
		)
	);

endif;

if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page( [
		'page_title' => esc_html__( 'Theme options', 'ucsc-2022' ),
		'menu_slug'  => esc_html__( 'ucsc-theme-options', 'ucsc-2022' ),
	] );
}

if( function_exists( 'acf_add_local_field_group' ) ) {

	acf_add_local_field_group( [
		'key' 					=> 'ucsc_theme_options_group',
		'title' 				=> 'Theme options',
		'fields' 				=> [
			[
				'key' 				=> 'ucsc_theme_options_group_tab',
				'label' 			=> esc_html__( 'General Options', 'ucsc-2022' ),
				'name' 				=> '',
				'aria-label' 		=> '',
				'type' 				=> 'tab',
				'instructions' 		=> '',
				'required' 			=> 0,
				'conditional_logic' => 0,
				'wrapper' 			=> [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'placement' 		=> 'top',
				'endpoint' 			=> 0,
			],
			[
				'key' 				=> 'ucsc_theme_options_group_universal_logo_visibility',
				'label' 			=> esc_html__( 'Universal Logo', 'ucsc-2022' ),
				'name' 				=> 'toggle_universal_logo_visibility',
				'aria-label' 		=> '',
				'type' 				=> 'true_false',
				'instructions' 		=> '',
				'required' 			=> 0,
				'conditional_logic' => 0,
				'wrapper' 			=> [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'message' 			=> esc_html__( 'Display', 'ucsc-2022' ),
				'default_value' 	=> 1,
				'ui_on_text' 		=> '',
				'ui_off_text' 		=> '',
				'ui' 				=> 0,
			],
		],
		'location' 				=> [
			[
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'ucsc-theme-options',
				],
			],
		],
		'menu_order' 			=> 0,
		'position' 				=> 'normal',
		'style' 				=> 'default',
		'label_placement' 		=> 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' 		=> '',
		'active' 				=> true,
		'description' 			=> '',
		'show_in_rest' 			=> 0,
	] );

}
