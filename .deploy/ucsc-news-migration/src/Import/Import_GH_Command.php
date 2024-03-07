<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use FastVolt\Helper\Markdown;
use TribeUCSC\Core;
use TribeUCSC\Import\Processors\Github_Post;
use TribeUCSC\Import\Traits\With_Log_Entry;
use function TribeUCSC\tribe_ucsc;

class Import_GH_Command extends Command {

	use With_Log_Entry;

	public function run( $args, $assoc_args ) {
		$single_file = $assoc_args['file'] ?? '';
		$force       = $assoc_args['force'] ?? false;

		\WP_CLI::colorize( esc_html__( 'Run GH import to posts conversion' ) );
		$path_to_records = sprintf( '%s_posts', tribe_ucsc()->get_container()->get( Core::PLUGIN_PATH ) );
		$files 		     = scandir( $path_to_records );

		if ( ! $files ) {
			$this->write_log( 'Failed to get records folder content', [
				'file_path' => $path_to_records,
			] );
			\WP_CLI::error( 'Failed to get records folder content' );

			return;
		}

		foreach ( $files as $file ) {
			if ( in_array( $file, [ '.', '..', '.gitignore' ] ) || ( ! empty( $single_file ) && $single_file !== $file ) ) {
				continue;
			}

			\WP_CLI::line( sprintf( 'Processing file: %s', $file ) );
			$this->write_log( 'Processing file:', [
				'file' => $file,
			] );

			$path_to_file = $path_to_records . '/' . $file;

			$content = file_get_contents( $path_to_file );

			if ( ! $content ) {
				\WP_CLI::line( sprintf( 'Could not parse the: %s', $file ) );
				$this->write_log( 'Unable get file content:', [
					'file' => $file,
				] );

				return;
			}

			$post_date      = strtotime( preg_replace( '/(^[\d]{4}\-[\d]{2}\-[\d]{2})(.*)/', '$1', $file ) );
			// Remove meta info from content
			$concat_content = substr( $content, strripos( $content, '---' ) + 3);
			// Prepare photos
			$concat_content = $this->prepare_content( $concat_content );
			// remove source link in the end of content
			$concat_content = preg_replace( '/\[Source\].*$/mi', '', $concat_content );
			$parser         = Markdown::new();
			$parser->setContent( $concat_content );

			$data = array_merge( [
				'post_date'    => date( 'Y-m-d H:i:s', (int) $post_date ),
				'post_status'  => 'publish',
				'post_content' => $this->convert_links_to_img( $parser->toHtml() ),
			], $this->get_meta_data( $path_to_file ) );

			$post_processor = new Github_Post( $data, (bool) $force );
			$result         = $post_processor->run();

			if ( ! $result ) {
				\WP_CLI::warning( sprintf( 'Skip post %s processing', $data['post_name'] ) );

				continue;
			}
			\WP_CLI::success( sprintf( 'Finish processing of the: %s', $data['post_name']  ) );
		}
	}

	protected function convert_links_to_img( string $content ) {
		preg_match_all( '/\<a.*href\=\"(.*)\".*\<\/a>/im', $content, $links );

		if ( empty( $links ) || empty( $links[1] ) ) {
			return $content;
		}

		foreach ( $links[1] as $key => $link ) {
			if ( ! str_ends_with( $link, '.gif' ) && ! str_ends_with( $link, '.jpg' ) && ! str_ends_with( $link, '.jpeg' ) && ! str_ends_with( $link, '.png' ) ) {
				continue;
			}

			$content = str_replace( $links[0][ $key ], sprintf( '<img src="%s" alt="" />', $link ), $content );
		}

		return $content;
	}

	protected function prepare_content( string $content ): string {
		preg_match_all( '/\!\[.*(\])\[\d\]/im', $content, $photos );

		if ( ! empty( $photos ) && ! empty( $photos[0] ) ) {
			foreach ( $photos[0] as $key => $photo ) {
				$replaced = str_replace( [ '[\[', '\]]'], [ '[', ']' ], $photo );
				$content  = str_replace( $photo, $replaced, $content );
			}
		}

		return $content;
	}

	protected function get_meta_data( string $file ): array {
		$handle     = fopen( $file, 'r' );
		$file_start = false;
		$result     = [
			'post_title'   => '',
			'authors' => [],
		];

		while ( ( $line = fgets( $handle ) ) !== false ) {

			if ( $file_start && stripos( $line, '---') !== false ) {
				fclose( $handle );

				return $result;
			}

			if ( stripos( $line, 'title: ') !== false ) {
				$result['post_title'] = trim( str_replace( 'title: ', '', $line ) );
				$result['post_name']  = sanitize_title( $result['post_title'] );
			}

			if ( stripos( $line, 'author: ') !== false ) {
				$authors = str_replace( 'author: ', '', $line );
				$authors = str_replace( [ '[', ']' ], '', $authors );

				$result['authors'] = explode( ', ', $authors);
			}

			if ( ! $file_start && stripos( $line, '---') !== false ) {
				$file_start = true;
			}
		}


		fclose( $handle );

		return $result;
	}

	/**
	 * Declare command name
	 * @return string
	 */
	protected function command(): string {
		return 'import github';
	}

	/**
	 * Add a command description
	 *
	 * @return string
	 */
	protected function description(): string {
		return esc_html__( 'Import old entries from GitHub', 'tribe' );
	}

	/**
	 * Declare command arguments
	 *
	 * @return array[]
	 */
	protected function arguments(): array {
		return [
			[
				'type'        => 'assoc',
				'name'        => 'file',
				'optional'    => true,
				'description' => esc_html__( 'file to process.', 'tribe' ),
			],
			[
				'type'        => 'assoc',
				'name'        => 'force',
				'optional'    => true,
				'description' => esc_html__( 'Override existing WP posts.', 'tribe' ),
			],
		];
	}

}
