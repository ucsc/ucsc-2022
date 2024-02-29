<?php declare(strict_types=1);

namespace TribeUCSC;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use TribeUCSC\Import\Import_Subscriber;
use TribeUCSC\Resource\Resource_Subscriber;
use TribeUCSC\Settings\Settings_Subscriber;

final class Core {

	public const PLUGIN_FILE        = 'plugin.file';
	public const PLUGIN_PATH        = 'plugin.path';
	public const VERSION_DEFINITION = 'plugin.version';
	public const VERSION            = '1.0.0';

	private ContainerInterface $container;

	private static self $instance;

	/**
	 * @var \Tribe\Libs\Container\Abstract_Subscriber[]
	 */
	private array $subscribers = [
		Import_Subscriber::class,
	];

	/**
	 * @var \Tribe\Libs\Container\Definer_Interface[]
	 */
	private array $definers = [];

	/**
	 * Singleton constructor.
	 *
	 * @return \TribeUCSC\Core
	 */
	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init( string $plugin_file ): void {
		$builder = new ContainerBuilder();
		$builder->useAutowiring( true );
		$builder->useAnnotations( false );
		$builder->addDefinitions( [ self::PLUGIN_FILE => $plugin_file ] );
		$builder->addDefinitions( [ self::PLUGIN_PATH => trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ) ] );
		$builder->addDefinitions( [ self::VERSION_DEFINITION => self::VERSION ] );
		$builder->addDefinitions( ...array_map( static function ( $classname ) {
			return ( new $classname() )->define();
		}, $this->definers ) );

		try {
			$this->container = $builder->build();
		} catch ( \Throwable $e ) {
			return;
		}

		foreach ( $this->subscribers as $subscriber_class ) {
			( new $subscriber_class( $this->container ) )->register();
		}
	}

	/**
	 * Returns the PHP-DI container.
	 *
	 * @return \Psr\Container\ContainerInterface
	 */
	public function get_container(): ContainerInterface {
		return $this->container;
	}

	private function __clone() {
	}

}
