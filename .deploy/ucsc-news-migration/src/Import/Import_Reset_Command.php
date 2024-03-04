<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use TribeUCSC\Core;
use TribeUCSC\DB\DB;
use TribeUCSC\Import\Processors\XML_Processor;
use TribeUCSC\Import\Traits\With_Log_Entry;
use function TribeUCSC\tribe_ucsc;

class Import_Reset_Command extends Command {

	use With_Log_Entry;

	private array $files = [];

	public function run( $args, $assoc_args ) {
		$type  = $assoc_args['type'] ?? 'soft';
		$wipe  = $assoc_args['wipe'] ?? '';
		$table = DB::get_table_name();
		global $wpdb;

		if ( $type === 'soft' ) {
			$wpdb->query( "UPDATE {$table} SET status = 0" );
		}

		if ( ! empty( $wipe ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'post'");
		}

		if ( $type === 'hard' ) {
			$wpdb->query( "DELETE FROM {$table}" );
		}
	}

	/**
	 * Declare command name
	 * @return string
	 */
	protected function command(): string {
		return 'import reset';
	}

	/**
	 * Add a command description
	 *
	 * @return string
	 */
	protected function description(): string {
		return esc_html__( 'Reset XML table with migration data', 'tribe' );
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
				'name'        => 'type',
				'optional'    => true,
				'description' => esc_html__( 'Type of the reset: soft or hard', 'tribe' ),
			],
			[
				'type'        => 'assoc',
				'name'        => 'wipe',
				'optional'    => true,
				'description' => esc_html__( 'Delete all posts', 'tribe' ),
			],
		];
	}

}
