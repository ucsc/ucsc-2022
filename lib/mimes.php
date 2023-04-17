<?php

add_filter('upload_mimes', function ( array $mimes ) {
	return array_merge( $mimes, [ 'svg' => 'image/svg+xml' ] );
}, 10, 1 );
