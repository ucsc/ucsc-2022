<?php declare(strict_types=1);

namespace TribeUCSC\Import\Processors;

class Github_Post extends Creator {

	private string $post_date;
	private string $post_status;
	private string $post_content;
	private string $post_title;
	private string $post_name;
	private array $authors;
	private bool $force;
	private int $existing_id = 0;

	public function __construct( array $args = [], bool $force = false ) {
		$this->post_date    = $args['post_date'];
		$this->post_status  = $args['post_status'] ?? 'publish';
		$this->post_content = $args['post_content'] ?? '';
		$this->post_title   = $args['post_title'];
		$this->post_name    = $args['post_name'];
		$this->authors      = $args['authors'] ?? [];
		$this->force        = $force;
	}

	public function run(): bool {
		try {
			if ( ! $this->force && $this->is_post_exists() > 0 ) {
				\WP_CLI::colorize( sprintf( 'The %s post exists with ID %d. Skipping ', $this->post_name, $this->existing_id ) );
				$this->write_log( 'Post exists. Skipping', [
					'page' => $this->post_title,
					'id'   => $this->existing_id,
				] );

				return true;
			}

			$post_id = $this->maybe_create_post( [
				'post_name'    => $this->post_name,
				'post_title'   => $this->post_title,
				'post_status'  => $this->post_status,
				'post_content' => $this->post_content,
				'post_date'    => $this->post_date,
			] );

			if ( $post_id < 1 ) {
				return false;
			}

			$this->assign_authors( $post_id );
			$this->assign_default_category( $post_id );
			$this->handle_images( $post_id );

			return true;
		} catch ( \Throwable $exception ) {
			$this->write_log( 'Could not process entity', [
				'page'  => $this->post_name,
				'error' => $exception->getMessage(),
				'code'  => $exception->getCode(),
				'trace' => $exception->getTraceAsString(),
			] );

			\WP_CLI::warning( sprintf( 'Could not process entity %s', $this->post_name ) );

			return false;
		}
	}

	protected function handle_images( int $post_id ): void {
		$post = get_post( $post_id );

		if ( is_wp_error( $post ) || (int) $post === 0 ) {
			return;
		}

		preg_match_all( '/\<img.*src=\"(.*)\".*\/>/im', $post->post_content, $img_tags );

		if ( empty( $img_tags ) || empty( $img_tags[1] ) ) {
			return;
		}

		foreach ( $img_tags[1] as $key => $link ) {
			$image_processor = new Image( $link, $post_id, '' );
			$image_id 		 = $image_processor->run();

			if ( is_wp_error( $image_id ) || (int) $image_id === 0 ) {
				continue;
			}

			$url = wp_get_attachment_image_url( $image_id, 'full' );

			if ( ! $url ) {
				false;
			}

			$post->post_content = str_replace( $link, $url, $post->post_content );
		}

		wp_update_post( [
			'ID' 		   => $post_id,
			'post_content' => $post->post_content
		] );
	}

	protected function assign_default_category( int $post_id ): void {
		$category_processor = new Category( $post_id, 'News' );
		$term      			= $category_processor->run();

		if ( $term < 1 ) {
			return;
		}

		wp_set_post_categories( $post_id, [ $term ] );
		\WP_CLI::line( sprintf( 'Finish post categories processing. Post ID: %d', $post_id ) ) ;
		$this->write_log( 'Finish post categories processing.', [
			'post_id' => $post_id,
		] );
	}

	protected function assign_authors( int $post_id ): void {
		foreach ( $this->authors as $author ) {
			$this->assign_co_author( $post_id, $author, '' );
		}
	}

	protected function is_post_exists(): int {
		$posts = get_posts( [
			'name'   => $this->post_name,
			'fields' => 'ids'
		] );

		if ( count( $posts ) < 1 ) {
			return 0;
		}

		$this->existing_id = reset( $posts );

		return $this->existing_id;
	}

}
