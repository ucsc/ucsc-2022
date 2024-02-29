<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use WP_CLI;

abstract class Command extends \WP_CLI_Command {

	public function register(): void {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return;
		}

		WP_CLI::add_command( 'ucsc ' . $this->command(), [ $this, 'run' ], [
			'shortdesc' => $this->description(),
			'synopsis'  => $this->arguments(),
		] );
	}

	abstract public function run( $args, $assoc_args );

	abstract protected function command();

	abstract protected function description();

	abstract protected function arguments();

}
