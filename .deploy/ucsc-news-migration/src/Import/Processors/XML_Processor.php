<?php declare(strict_types=1);

namespace TribeUCSC\Import\Processors;

use TribeUCSC\DB\DB;
use TribeUCSC\Import\Traits\With_Log_Entry;

class XML_Processor {

	use With_Log_Entry;

	private string $file_name;
	private string $file_path;

	public function __construct( string $file_name, string $file_path) {
		$this->file_name = $file_name;
		$this->file_path = $file_path;
	}

	public function run(): bool {
		try {
			$xml_content = simplexml_load_file( $this->file_path );

			if ( ! $xml_content ) {
				$this->write_log( 'Could not load XML file', [
					'file_name' => $this->file_name,
					'file_path' => $this->file_path,
				] );

				\WP_CLI::line( sprintf( 'Could not load XML file %s', $this->file_name ) );

				return false;
			}

			global $wpdb;

			foreach ( $xml_content->{'system-page'} as $node ) {
				if ( (string) $node->name === 'index' ) {
					continue;
				}
				$html 	    = '';
				$cascade_id = (string) $node->attributes()['id'];
				$exists     = $this->is_existed_entry( $cascade_id );

				if ( $node->{'system-data-structure'}->{'article-text'} ) {
					$html = str_replace( ['<article-text>', '</article-text>'], '', $node->{'system-data-structure'}->{'article-text'}->asXML() );
				}

				$args = [
					'date_added' => date( 'm-d-Y H:i:s', time() ),
					'file'		 => $this->file_name,
					'cascade_id' => $cascade_id,
					'page_name'  => (string) $node->name,
					'content'	 => json_encode( $node ),
					'post_html'	 => $html,
					'status'	 => 0,
				];

				if ( ! $exists ) {
					$wpdb->insert( DB::get_table_name(), $args );

					continue;
				}

				$wpdb->update( DB::get_table_name(), $args, [ 'cascade_id' => $cascade_id, ] );
			}

			return true;
		} catch ( \Throwable $exception ) {
			$this->write_log( 'Could not process file', [
				'file_name' => $this->file_name,
				'file_path' => $this->file_path,
				'error'     => $exception->getMessage(),
				'code'      => $exception->getCode(),
				'trace'     => $exception->getTraceAsString(),
			] );

			\WP_CLI::error( sprintf( 'Could not process file %s %s', $this->file_name, $this->file_path ) );

			return false;
		}
	}

	protected function is_existed_entry( string $cascade_id ): ?string {
		global $wpdb;
		$table = DB::get_table_name();

		return $wpdb->get_var( $wpdb->prepare( "SELECT cascade_id FROM {$table} WHERE cascade_id = %s", $cascade_id ) );

	}

}
