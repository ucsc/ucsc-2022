<?php declare(strict_types=1);

namespace TribeUCSC\Import\Processors;

class Post extends Creator {

	private string $cascade_id;
	private string $raw_data;
	private string $page_name;
	private string $post_html;
	private bool $force;
	private int $existing_id = 0;

	public function __construct( string $page_name, string $raw_data, string $cascade_id, string $post_html, bool $force ) {
		$this->page_name  = $page_name;
		$this->cascade_id = $cascade_id;
		$this->raw_data   = $raw_data;
		$this->post_html  = $post_html;
		$this->force      = $force;
	}

	public function run(): bool {
		try {
			if ( ! $this->force && $this->is_post_exists() > 0 ) {
				\WP_CLI::colorize( sprintf( 'The %s post exists with ID %d. Skipping ', $this->page_name, $this->existing_id ) );
				$this->write_log( 'Post exists. Skipping', [
					'page' => $this->page_name,
					'id'   => $this->existing_id,
				] );

				return true;
			}

			$data = $this->parse_raw_data();

			if ( count( $data ) < 1 ) {
				return false;
			}
			$meta    			 = $data['meta'];
			$categories 		 = $data['categories'];
			$has_secondary_image = $data['has_secondary_image'];

			unset( $data['meta'] );
			unset( $data['categories'] );
			unset( $data['has_secondary_image'] );

			$post_id = $this->maybe_create_post( $data );

			if ( $post_id < 1 ) {
				return false;
			}

			$this->process_post_meta( $post_id, $meta, $has_secondary_image );
			$this->process_post_categories( $post_id, $categories );

			return true;
		} catch ( \Throwable $exception ) {
			$this->write_log( 'Could not process entity', [
				'page'  => $this->page_name,
				'error' => $exception->getMessage(),
				'code'  => $exception->getCode(),
				'trace' => $exception->getTraceAsString(),
			] );

			\WP_CLI::warning( sprintf( 'Could not process entity %s', $this->page_name ) );

			return false;
		}
	}

	protected function process_post_meta( int $post_id, array $meta_data = [], bool $has_secondary_image = false ): void {
		\WP_CLI::line( sprintf( 'Processing post meta. Post ID: %d', $post_id ) ) ;
		$this->write_log( 'Processing post meta.', [
			'post_id' => $post_id,
		] );

		foreach ( $meta_data as $meta_key => $meta_value ) {
			// TODO: skip images for now until we have clear vision on that
			if ( in_array( $meta_key, [ 'cascade_lead_image', 'cascade_secondary_images' ] ) ) {
				update_post_meta( $post_id, $meta_key, $meta_value );
				// skip empty
				if ( ! isset( $meta_value['image'] ) || ! isset( $meta_value['image']['link'] ) ) {
					continue;
				}

				$image_processor = new Image( $meta_value['image']['link'], $post_id, $meta_key, $meta_value );
				$image_id 		 = $image_processor->run();

				if ( is_wp_error( $image_id ) || (int) $image_id === 0 ) {
					continue;
				}

				$this->inject_image_block( $post_id, $image_id, $meta_key, $meta_value, $has_secondary_image );

				continue;
			}

			update_post_meta( $post_id, $meta_key, $meta_value );

			if ( $meta_key === 'cascade_subhead' && ! empty( $meta_value ) ) {
				update_post_meta( $post_id, 'subtitle-copy', $meta_value );
				update_post_meta( $post_id, '_subtitle-copy', 'field_6245e37f6a110' );

				continue;
			}

			if ( $meta_key === 'cascade_contact' && class_exists( 'CoAuthors_Plus' ) ) {
				$this->inject_co_authors( $meta_value, $post_id );
			}
		}

		\WP_CLI::line( sprintf( 'Finish post meta processing. Post ID: %d', $post_id ) ) ;
		$this->write_log( 'Finish post meta processing.', [
			'post_id' => $post_id,
		] );
	}

	protected function inject_co_authors( array $author, int $post_id ) {
		if ( empty( $author['name'] ) ) {
			return;
		}

		$this->assign_co_author( $post_id, $author['name'] ?? '', $author['email'] ?? '' );
	}

	protected function inject_image_block( int $post_id, int $image_id, string $meta_key, array $meta = [], bool $has_secondary_image = false ): void {
		if ( ( $has_secondary_image && $meta_key === 'cascade_lead_image' ) || ( ! $has_secondary_image && $meta_key !== 'cascade_secondary_images' ) ) {
			return;
		}

		$line_1		   = sprintf( '<!-- wp:image {"align":"right","id":%s,"sizeSlug":"full","linkDestination":"none"} -->', $image_id );
		$img_src       = sprintf( '<img src="%s" alt="" class="wp-image-%s"/>', wp_get_attachment_image_src( $image_id, 'full' )[0], $image_id );
		$image_caption = '';
		$caption 	   = is_array( $meta['image-caption'] ) ? implode( ' ', $meta['image-caption'] ) : $meta['image-caption'];
		if ( strlen( $caption ) > 0 ) {
			$image_caption .= sprintf( '<figcaption class="wp-element-caption">%s</figcaption>', $caption );
		}
		$image_block = <<<EOF
$line_1
<figure class="wp-block-image alignright size-full">$img_src$image_caption</figure>
<!-- /wp:image -->


EOF;

		$this->write_log( 'Trying to inject image block.', [
			'post_id' 		=> $post_id,
			'image_caption' => $image_caption,
			'image_block'   => $image_block,
		] );
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$post_content = $image_block . $post->post_content;

		wp_update_post( [
			'ID' 		   => $post_id,
			'post_content' => $post_content,
		] );
	}

	protected function process_post_categories( int $post_id, array $categories ): void {
		\WP_CLI::line( sprintf( 'Processing post categories. Post ID: %d', $post_id ) ) ;
		$this->write_log( 'Processing post categories.', [
			'post_id' => $post_id,
		] );

		$post_categories = [];
		foreach ( $categories as $category ) {
			if ( ! isset( $category['name'] ) ) {
				continue;
			}

			// Process parent categories
			$parent_name = str_replace( 'category-', '', $category['name'] );
			$processor 	 = new Category( $post_id, $parent_name );
			$parent_id   = $processor->run();

			// There are no categories assigned directly to post
			if ( ! isset( $category['value'] ) || $parent_id < 1 ) {
				continue;
			}

			if ( is_array( $category['value'] ) ) {
				foreach ( $category['value'] as $item ) {
					$processor = new Category( $post_id, $item, $parent_id );
					$term = $processor->run();

					if ( $term < 1 ) {
						continue;
					}

					$post_categories[] = $term;
				}

				continue;
			}

			$processor = new Category( $post_id, $category['value'], $parent_id );
			$term      = $processor->run();

			if ( $term < 1 ) {
				continue;
			}

			$post_categories[] = $term;
		}

		wp_set_post_categories( $post_id, $post_categories );

		\WP_CLI::line( sprintf( 'Finish post categories processing. Post ID: %d', $post_id ) ) ;
		$this->write_log( 'Finish post categories processing.', [
			'post_id' => $post_id,
		] );
	}

	protected function parse_raw_data(): array {
		if ( strlen( $this->raw_data ) < 1 ) {
			\WP_CLI::warning( sprintf( 'The %s post does not have data to process ', $this->page_name ) );

			$this->write_log( 'Post raw content is empty', [
				'page' => $this->page_name,
			] );

			return [];
		}

		$data = json_decode( $this->raw_data, true );

		if ( count( $data ) < 1 ) {
			\WP_CLI::warning( sprintf( 'Could not decode post %s json data', $this->page_name ) );

			$this->write_log( 'Could not decode post json data', [
				'page' => $this->page_name,
				'json' => $this->raw_data,
			] );

			return [];
		}

		$post_date = ceil( (int) $data['start-date'] / 1000 );

		return [
			'post_name'    		  => is_numeric( $data['name'] ) ? sanitize_title( $data['title'] ) : $data['name'],
			'post_title'   		  => $data['title'],
			'post_type'    		  => 'post',
			'post_excerpt' 		  => $data['summary'] ?? '',
			'post_date'	  		  => date( 'Y-m-d H:i:s', (int) $post_date ),
			'post_status' 		  => $data['is-published'] === 'true' ? 'publish' : 'draft',
			'post_content'		  => $this->post_html ?? '',
			'categories'   		  => $data['dynamic-metadata'],
			'has_secondary_image' => $data['system-data-structure']['secondary-images'] && isset( $data['system-data-structure']['secondary-images']['image'] ) && ! empty( $data['system-data-structure']['secondary-images']['image']['path'] ),
			'meta'   	   => [
				'cascade_id'   			   => $this->cascade_id,
				'cascade_path' 			   => $data['path'],
				'cascade_author' 		   => $data['created-by'],
				'cascade_keywords' 		   => $data['keywords'] ?? '',
				'cascade_contact' 		   => $data['system-data-structure']['contact'],
				'cascade_video'   		   => $data['system-data-structure']['video'],
				'cascade_lead_image'   	   => $data['system-data-structure']['lead-image'],
				'cascade_secondary_images' => $data['system-data-structure']['secondary-images'],
				'cascade_subhead'		   => $data['system-data-structure']['article-subhead'],
				'cascade_raw_data'         => $this->raw_data,
			]
		];
	}

	protected function is_post_exists(): int {
		$posts = get_posts( [
			'name'   => $this->page_name,
			'fields' => 'ids'
		] );

		if ( count( $posts ) < 1 ) {
			return 0;
		}

		$this->existing_id = reset( $posts );

		return $this->existing_id;
	}

}
