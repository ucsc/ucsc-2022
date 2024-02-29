<?php declare(strict_types=1);

namespace TribeUCSC\Import\Processors;

use TribeUCSC\Import\Traits\With_Log_Entry;

class Image {

	use With_Log_Entry;

	public const SOURCE_URL = 'ucsc_cascade_image_url';

	private string $image_url;
	private int $post_id;
	private array $image_data;
	private bool $is_featured;

	public function __construct( string $image_path, int $post_id, string $type, array $image_data = [] ) {
		$this->image_url   = str_replace( 'site://news/', 'https://news.ucsc.edu/', $image_path );
		$this->post_id 	   = $post_id;
		$this->image_data  = $image_data;
		$this->is_featured = $type === 'cascade_lead_image';
	}

	public function run() {
		$this->require_files();

		$tmp = download_url( $this->image_url );

		if ( is_wp_error( $tmp ) ) {
			$this->write_log( 'Unable to process image', [
				'image'   => $this->image_url,
				'post_id' => $this->post_id,
			] );

			return 0;
		}

		$path = parse_url( $this->image_url, PHP_URL_PATH );

		$file_array = [
			'name'     => basename( $path ),
			'tmp_name' => $tmp,
		];

		$image_id = media_handle_sideload( $file_array, $this->post_id );

		if ( is_wp_error( $image_id ) ) {
			if ( file_exists( $tmp ) ) {
				unlink( $tmp );
			}

			$this->write_log( 'Failed to sideload image', [
				'image'   => $this->image_url,
				'post_id' => $this->post_id,
				'error' => $image_id->get_error_messages(),
			] );

			return 0;
		}

		if ( $this->is_featured ) {
			set_post_thumbnail( $this->post_id, $image_id );
		}

		update_post_meta( $this->post_id, self::SOURCE_URL, $this->image_url );
		$this->write_log( 'Image data', [
			'post_id' => $this->post_id,
			'data'    => $this->image_data['image-alt'],
		] );

		if ( empty( $this->image_data['image-alt'] ) ) {
			return $image_id;
		}

		if ( strlen( $this->image_data['image-alt'] ) > 0 ) {
			update_post_meta( $this->post_id, '_wp_attachment_image_alt', (string) $this->image_data['image-alt'] );
		}

		return $image_id;
	}

	private function require_files(): void {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}

}
