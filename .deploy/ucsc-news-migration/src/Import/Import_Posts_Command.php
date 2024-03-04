<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use TribeUCSC\DB\DB;
use TribeUCSC\Import\Processors\Post;
use TribeUCSC\Import\Traits\With_Log_Entry;

class Import_Posts_Command extends Command {

	use With_Log_Entry;

	public const BATCH_SIZE = 1000;

	public function run( $args, $assoc_args ) {
		$file  = $assoc_args['file'] ?? '';
		$page  = $assoc_args['page'] ?? '';
		$force = $assoc_args['force'] ?? false;

		\WP_CLI::colorize( esc_html__( 'Run xml to posts conversion' ) );
		$this->write_log( 'Run xml to posts conversion', [
			'file' => $file,
			'page' => $page,
		] );

		global $wpdb;

		$table_name = DB::get_table_name();
		$sql 	    = "SELECT * FROM {$table_name} WHERE page_name != 'index'";

		if ( ! $force ) {
			$sql .= " AND status != 1";
		}

		if ( strlen( $file ) > 0 ) {
			$sql .= " AND file = '" . $file . "'";
		}

		if ( strlen( $page ) > 0 ) {
			$sql .= " AND page_name = '" . $page . "'";
		}

		$sql .= "ORDER BY file ASC LIMIT 0, " . self::BATCH_SIZE;

		$records = $wpdb->get_results( $sql );

		if ( is_wp_error( $records ) || count( $records ) < 1 ) {
			$this->write_log( 'Could not find XML records', [
				'file' => $file,
				'page' => $page,
				'sql'  => $sql,
			] );

			\WP_CLI::error( esc_html__( 'There are no records to process. Please make sure that you import XML data first', 'tribe' ) );

			return;
		}

		foreach ( $records as $record ) {
			\WP_CLI::line( sprintf( 'Processing page: %s', $record->page_name ) );

			$this->write_log( 'Processing page', [
				'page' => $record->page_name,
			] );

			$post_processor = new Post( $record->page_name, $record->content, $record->cascade_id, $record->post_html, (bool) $force );
			$result         = $post_processor->run();

			if ( ! $result ) {
				\WP_CLI::warning( sprintf( 'Skip post %s processing', $record->page_name ) );

				continue;
			}

			$wpdb->update( $table_name, [ 'status' => 1, ], [ 'cascade_id' => $record->cascade_id ] );
			\WP_CLI::success( sprintf( 'Finish processing of the: %s', $record->page_name ) );
		}
	}

	protected function command(): string {
		return 'import posts';
	}

	protected function description(): string {
		return esc_html__( 'Converts raw XML data to posts', 'tribe' );
	}

	protected function arguments(): array {
		return [
			[
				'type'        => 'assoc',
				'name'        => 'file',
				'optional'    => true,
				'description' => esc_html__( 'XML file to process.', 'tribe' ),
			],
			[
				'type'        => 'assoc',
				'name'        => 'page',
				'optional'    => true,
				'description' => esc_html__( 'Single XML page to process.', 'tribe' ),
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
