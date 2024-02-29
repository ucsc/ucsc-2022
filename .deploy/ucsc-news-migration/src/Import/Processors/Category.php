<?php declare(strict_types=1);

namespace TribeUCSC\Import\Processors;

use TribeUCSC\Import\Traits\With_Log_Entry;

class Category {

	use With_Log_Entry;

	private string $name;
	private int $parent;
	private int $post_id;
	private int $existing_id;

	public function __construct( int $post_id, string $name, int $parent = 0 ) {
		$this->post_id = $post_id;
		$this->name    = $name;
		$this->parent  = $parent;
	}

	public function run(): bool|int {
		\WP_CLI::line( sprintf( 'Processing  %s category ', $this->name ) );

		$this->write_log( 'Processing category', [
			'category' => $this->name,
		] );
		try {
			if ( $this->is_category_exists() ) {
				\WP_CLI::colorize( sprintf( 'The %s category exists with ID %d. Skipping ', $this->name, $this->existing_id ) );

				$this->write_log( 'Post exists. Skipping', [
					'category' => $this->name,
					'id'   	   => $this->existing_id,
				] );

				return $this->existing_id;
			}

			return $this->maybe_create_category();
		} catch ( \Throwable $exception ) {
			$this->write_log( 'Could not process category', [
				'post_id'  => $this->post_id,
				'category' => $this->name,
				'error'    => $exception->getMessage(),
				'code'     => $exception->getCode(),
				'trace'    => $exception->getTraceAsString(),
			] );

			\WP_CLI::warning( sprintf( 'Could not process category %s for post %d', $this->name, $this->post_id ) );

			return false;
		}
	}

	protected function maybe_create_category(): int {
		$args = [
			'slug' => sanitize_title( $this->name ),
		];

		if ( $this->parent !== 0 ) {
			$args['parent'] = $this->parent;
		}

		$term = wp_insert_term( ucfirst( $this->name ), 'category', $args );

		if ( is_wp_error( $term ) ) {
			/**
			 * @var \WP_Error $term
			 */
			$this->write_log( 'Could not create category', [
				'post_id' => $this->post_id,
				'name'    => $this->name,
				'error'   => $term->get_error_messages(),
			] );

			\WP_CLI::warning( sprintf( 'Could not create category %s', $this->name ) );

			return 0;
		}

		\WP_CLI::line( sprintf( 'Category  %s created with id %d', $this->name, (int) $term['term_id'] ) );

		$this->write_log( 'Category created', [
			'category' => $this->name,
			'id' 	   => (int) $term['term_id'],
		] );

		return (int) $term['term_id'];
	}

	protected function is_category_exists(): int {
		$term = get_term_by( 'name', $this->name, 'category' );

		if ( ! $term ) {
			return 0;
		}

		$this->existing_id = $term->term_id;

		return $this->existing_id;
	}

}
