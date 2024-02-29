<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use TribeUCSC\Core;
use TribeUCSC\Import\Processors\XML_Processor;
use TribeUCSC\Import\Traits\With_Log_Entry;
use function TribeUCSC\tribe_ucsc;

class Import_Files_Command extends Command {

	use With_Log_Entry;

	public function run( $args, $assoc_args ) {
		$path_to_records = sprintf( '%srecords', tribe_ucsc()->get_container()->get( Core::PLUGIN_PATH ) );

		for ( $year = 2002; $year <= date( 'Y' ); $year++ ) {
			for ( $month = 1; $month <= 12; $month++ ) {
				$date = sprintf( '%d/%s', $year, str_pad( (string) $month, 2, '0', STR_PAD_LEFT ) );
				$path = sprintf( 'https://news.ucsc.edu/%s/index.xml', $date );

				\WP_CLI::line( sprintf( 'Download file: %s', $path ) );

				$content = file_get_contents( $path );

				if ( ! $content || strlen( $content ) < 1 ) {
					\WP_CLI::line( sprintf( 'File does not exist: %s', $path ) );

					continue;
				}

				$year_folder = $path_to_records . '/' . $year;
				if ( ! is_dir( $year_folder ) ) {
					mkdir( $year_folder );

					\WP_CLI::line( sprintf( 'Create year folder: %s', $year ) );
				}

				$month_folder = $year_folder . '/' . $month;

				if ( ! is_dir( $month_folder ) ) {
					mkdir( $month_folder );

					\WP_CLI::line( sprintf( 'Create month folder: %s', $month ) );
				}

				$file = sprintf( '%s/index.xml', $month_folder );
				try {
					file_put_contents( $file, $content );
					\WP_CLI::success( sprintf( 'Success: %s', $file ) );
				} catch ( \Throwable $exception ) {
					\WP_CLI::error( sprintf( 'Can not save data: %s', $file ) );
				}
			}
		}


	}

	/**
	 * Declare command name
	 * @return string
	 */
	protected function command(): string {
		return 'import xmlfiles';
	}

	/**
	 * Add a command description
	 *
	 * @return string
	 */
	protected function description(): string {
		return esc_html__( 'Imports xml data files to records folder', 'tribe' );
	}

	/**
	 * Declare command arguments
	 *
	 * @return array[]
	 */
	protected function arguments(): array {
		return [];
	}

}
