<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use TribeUCSC\Core;
use TribeUCSC\Import\Processors\XML_Processor;
use TribeUCSC\Import\Traits\With_Log_Entry;
use function TribeUCSC\tribe_ucsc;

class Import_XML_Command extends Command {

	use With_Log_Entry;

	private array $files = [];

	public function run( $args, $assoc_args ) {
		$path_to_records = sprintf( '%srecords', tribe_ucsc()->get_container()->get( Core::PLUGIN_PATH ) );
		$files 		     = $this->get_folder_tree( $path_to_records );
		$single_file 	 = $assoc_args['file'] ?? '';
		$over_year	     = $assoc_args['over_year'] ?? '';

		natsort($files );

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

			preg_match( "/(?>records\/)(?'year'[0-9]*)\//i", $file, $year );

			if ( ! empty( $year ) && ! empty( $year['year'] ) && ! empty( $over_year ) ) {
				if ( (int) $year['year'] < (int) $over_year ) {
					continue;
				}
			}

			\WP_CLI::line( sprintf( 'Processing XML file: %s', $file ) );

			$this->write_log( 'Process XML file', [
				'file' => $file,
			] );

			$name 		   = explode( '/records', $file );
			$xml_processor = new XML_Processor( $name[1], $file );
			$result 	   = $xml_processor->run();

			if ( ! $result ) {
				\WP_CLI::error( 'Abort XML processing' );

				return;
			}
			\WP_CLI::success( sprintf( 'Finish XML file processing: %s', $file ) );
			$this->write_log( 'Process XML file successfully finished', [
				'file' => $file,
			] );
		}

	}

	protected function get_folder_tree( string $path ): array {
		$files = scandir( $path );

		foreach ( $files as $file ) {
			if ( in_array( $file, [ '.', '..', '.gitignore' ] ) ) {
				continue;
			}

			$file_path = $path . '/' . $file;
			if ( ! is_file( $file_path ) ) {
				$this->get_folder_tree( $file_path );
			} else {
				$this->files[] = $file_path;
			}
		}

		return $this->files;
	}

	/**
	 * Declare command name
	 * @return string
	 */
	protected function command(): string {
		return 'import xmldata';
	}

	/**
	 * Add a command description
	 *
	 * @return string
	 */
	protected function description(): string {
		return esc_html__( 'Imports raw xml data to DB', 'tribe' );
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
				'name'        => 'over_year',
				'optional'    => true,
				'description' => esc_html__( 'file to process.', 'tribe' ),
			],
		];
	}

}
