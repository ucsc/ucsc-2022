<?php

add_action( 'init', function () {
	// Register Main Nav Block
	register_block_type( get_template_directory() . '/blocks/main-nav' );
} );
