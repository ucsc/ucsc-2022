<?php declare(strict_types=1);

namespace TribeUCSC\Import\Traits;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use TribeUCSC\Core;
use function TribeUCSC\tribe_ucsc;

trait With_Log_Entry {

	public function log_message( string $message = '', array $context = [] ): string {
		$message = sprintf( '[%s]: %s', date( 'm-d-Y H:i:s' ), $message );

		return sprintf( '%s. Context: %s', $message, wp_json_encode( $context ) );
	}

	public function read_log(): array {
		$path = sprintf( '%slogs/import.log', tribe_ucsc()->get_container()->get( Core::PLUGIN_PATH ) );
		$file = fopen( $path, 'r' );

		if ( ! $file ) {
			return [];
		}

		$result = [];
		while ( ( $line = fgets( $file ) ) !== false ) {
			$result[] = sprintf( '<p>%s</p>', $line );
		}

		fclose( $file );

		return $result;
	}

	public function write_log( string $message, $context = [] ): void {
		if ( strlen( $message ) < 1 ) {
			return;
		}

		$path    = sprintf( '%slogs/import.log', tribe_ucsc()->get_container()->get( Core::PLUGIN_PATH ) );
		$logger  = new Logger( 'migration_log' );
		$handler = new StreamHandler( $path, Level::Info );
		$handler->setFormatter( ( new LineFormatter() ) );
		$logger->pushHandler( $handler );

		$logger->info( $message, $context );
	}

	public function get_log_size_mb( $path ): float|int {
		if ( ! file_exists( $path ) ) {
			return 0;
		}

		/**
		 * Get log size in bytes
		 */
		$file_size = filesize( $path );

		/**
		 * Return the size of log file in MB
		 */
		return round( $file_size / 1024 / 1024, 1 );
	}

}
