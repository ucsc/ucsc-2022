<?php declare(strict_types=1);

class Image_Sizes {

	public const SQUARE_SMALL = 'square-small';
	public const SQUARE_MEDIUM = 'square-medium';
	public const SQUARE_LARGE = 'square-large';
	public const SIXTEEN_NINE = 'sixteen-nine';
	public const SIXTEEN_NINE_SMALL = 'sixteen-nine-small';
	public const SIXTEEN_NINE_LARGE = 'sixteen-nine-large';

	/**
	 * @var array<string, array<string, int|bool>>
	 */
	private array $sizes = [
		self::SQUARE_SMALL => [
			'width' => 240,
			'height' => 240,
			'crop' => true,
		],
		self::SQUARE_MEDIUM => [
			'width' => 400,
			'height' => 400,
			'crop' => true,
		],
		self::SQUARE_LARGE => [
			'width' => 800,
			'height' => 800,
			'crop' => true,
		],
		self::SIXTEEN_NINE_SMALL => [
			'width' => 400,
			'height' => 248,
			'crop' => true,
		],
		self::SIXTEEN_NINE => [
			'width' => 800,
			'height' => 496,
			'crop' => true,
		],
		self::SIXTEEN_NINE_LARGE => [
			'width' => 1200,
			'height' => 744,
			'crop' => true,
		],
	];

	/**
	 * @return mixed
	 */
	public function get_sizes(): mixed {
		return apply_filters('ucsc/image/sizes', $this->sizes);
	}

	/**
	 * @action after_setup_theme
	 */
	public function register_sizes(): void {
		foreach ( $this->sizes as $key => $attributes ) {
			add_image_size( $key, $attributes['width'], $attributes['height'], $attributes['crop'] );
		}
	}

	public function register_size_names(): void {
		add_filter( 'image_size_names_choose', function ( array $sizes ) {
			$ucsc_sizes = [
				self::SQUARE_SMALL => __('Square Small', 'ucsc'),
				self::SQUARE_MEDIUM => __('Square Medium', 'ucsc'),
				self::SQUARE_LARGE => __('Square Large', 'ucsc'),
				self::SIXTEEN_NINE_SMALL => __('16:9 Small', 'ucsc'),
				self::SIXTEEN_NINE => __('16:9 Medium', 'ucsc'),
				self::SIXTEEN_NINE_LARGE => __('16:9 Large', 'ucsc'),
			];

			foreach ( $ucsc_sizes as $key => $value) {
				$sizes[ $key ] = $value;
			}

			return $sizes;
		} );
	}

}
