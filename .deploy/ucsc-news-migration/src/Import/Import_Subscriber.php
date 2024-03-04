<?php declare(strict_types=1);

namespace TribeUCSC\Import;

use Tribe\Libs\Container\Abstract_Subscriber;

class Import_Subscriber extends Abstract_Subscriber {

	public function register(): void{
		add_action( 'init', function() {
			$this->container->get( Import_XML_Command::class )->register();
			$this->container->get( Import_Files_Command::class )->register();
			$this->container->get( Import_Posts_Command::class )->register();
			$this->container->get( Import_Reset_Command::class )->register();
		}, 10, 0 );
	}

}
