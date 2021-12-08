<?php

	namespace MehrIt\LeviImages\Raster;

	use Contao\ImagineSvg\Image;
	use Imagine\Gd\Imagine as ImagineGd;
	use Imagine\Gmagick\Imagine as ImagineGmagick;
	use Imagine\Image\BoxInterface;
	use Imagine\Image\ImageInterface;
	use Imagine\Image\ImagineInterface;
	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Imagick\Imagine as ImagineImagick;
	use Imagine\Vips\Imagine;
	use Imagine\Vips\Imagine as ImagineVips;
	use MehrIt\LeviImages\Util\TemporaryFiles;
	use RuntimeException;

	class RasterImageFactory
	{ 
		use TemporaryFiles;
		
		const DRIVER_GD = 'gd';
		const DRIVER_IMAGICK = 'imagick';
		const DRIVER_GMAGICK = 'gmagick';
		const DRIVER_VIPS = 'vips';

		/**
		 * @var array
		 */
		protected $config;

		/**
		 * @var string|null
		 */
		protected $driver;

		/**
		 * @param array $config
		 */
		public function __construct(array $config = []) {
			$this->config = $config;
		}


		/**
		 * Gets the implemented drivers
		 * @return string[] The drivers
		 */
		public function drivers(): array {
			
			// Note: the order is imported for auto driver selection
			return [
				self::DRIVER_IMAGICK,
				self::DRIVER_GMAGICK,
				self::DRIVER_VIPS,
				self::DRIVER_GD,
			];
		}

		/**
		 * Checks if the given driver is supported
		 * @param string $driver The driver
		 * @return bool True if supported. Else false.
		 */
		public function isDriverSupported(string $driver): bool {

			switch ($driver) {
				case self::DRIVER_VIPS:
					return class_exists('Imagine\Vips\Imagine') && extension_loaded('vips');
				case self::DRIVER_IMAGICK:
					return $this->tryCreateImagine(ImagineImagick::class);
				case self::DRIVER_GMAGICK:
					return $this->tryCreateImagine(ImagineGmagick::class);
				case self::DRIVER_GD:
					return $this->tryCreateImagine(ImagineGd::class);

				default:
					return false;
			}
		}


		/**
		 * Gets the name of the driver to use.
		 * @return string The driver name
		 */
		public function driver(): string {
			
			if (!$this->driver) {


				$driverName = $this->config['driver'] ?? 'auto';

				switch ($driverName) {
					case 'auto':

						// return first supported driver
						foreach ($this->drivers() as $currDriver) {
							if ($this->isDriverSupported($currDriver)) {
								$this->driver = $currDriver;
								break 2;
							}
						}

						throw new RuntimeException('No raster image driver could be used. You must install "rokka/imagine-vips" or one of the PHP extensions "imagick", "gmagick" or "gd".');

					default:

						if (!$this->isDriverSupported($driverName))
							throw new RuntimeException("The selected driver \"{$driverName}\" is not supported on your system.");

						$this->driver = $driverName;
				}
			}
			
			return $this->driver;
		}

		/**
		 * Creates a new instance using the given driver
		 * @param string $driver The driver name
		 * @return RasterImageFactory The new instance
		 */
		public function useDriver(string $driver): RasterImageFactory {

			if (!$this->isDriverSupported($driver))
				throw new RuntimeException("The selected driver \"{$driver}\" is not supported on your system.");
			
			$conf = array_merge($this->config, [
				'driver' => $driver,
			]);
			
			return new static($conf);			
		}

		/**
		 * Loads a raster image from given file
		 * @param string $filename The filename
		 * @param array $loadOptions The load options. This is only supported by the 'vips' driver and ignored for all other drivers.
		 * @return ImageInterface The image
		 */
		public function open(string $filename, array $loadOptions = []): ImageInterface {

			return $this->imagine()->open($filename, $loadOptions);
		}


		/**
		 * Loads a raster image from given buffer
		 * @param string $buffer The buffer
		 * @param array $loadOptions The load options. This is only supported by the 'vips' driver and ignored for all other drivers.
		 * @return ImageInterface The image
		 */
		public function load(string $buffer, array $loadOptions = []): ImageInterface {

			$imagine = $this->imagine();
			
			// Imagick has problems loading WEBP images from blob. Therefore, we use a temporary file instead.
			if (substr($buffer, 0, 4) == 'RIFF' && (($imagine instanceof ImagineImagick) || ($imagine instanceof ImagineGmagick))) {
				return $this->withTempFile(function($file) use (&$buffer, $loadOptions) {
					
					$res = file_put_contents($file, $buffer);
					if ($res === false)
						throw new RuntimeException('Failed to write to temporary file.');
					
					return $this->open($file, $loadOptions);
				});
			}
			
			return $this->imagine()->load($buffer, $loadOptions);
		}

		/**
		 * Loads a raster image from given resource
		 * @param resource $source The resource to read from
		 * @param array $loadOptions The load options. This is only supported by the 'vips' driver and ignored for all other drivers.
		 * @return ImageInterface The image
		 */
		public function read($source, array $loadOptions = []): ImageInterface {

			$content = stream_get_contents($source);
			if ($content === false)
				throw new RuntimeException("Failed read from resource.");
			
			// we simply use load because passing a resource has no advantage for any driver so far
			return $this->load($content, $loadOptions);
		}

		/**
		 * Creates a new empty image with an optional background color.
		 * @param BoxInterface $size The size
		 * @param ColorInterface $color The background color
		 * @return ImageInterface
		 */
		public function create(BoxInterface $size, ColorInterface $color): ImageInterface {
			return $this->imagine()->create($size, $color);
		}

		/**
		 * Imports the given image for the current driver. If image instance is of other driver or a vector image, it is exported and re-imported using 
		 * the current driver.
		 * @param ImageInterface $image The image
		 * @return ImageInterface The driver image
		 */
		public function import(ImageInterface $image): ImageInterface {
			
			// if the image to import is of same type as expected by the current driver, we can 
			// use copy to import the image.
			$driverImageClass = $this->driverImageClass();
			if ($image instanceof $driverImageClass) {
				return $image->copy();
			}
			
			// determine possible export/import formats
			$exportFormats = $this->conversionExportFormats($image);
			$importFormats = $this->conversionDriverFormats(false);
			
			// choose a format for conversion
			$format = array_values(array_intersect($exportFormats, $importFormats))[0] ?? null;
			if (!$format)
				throw new RuntimeException('Importing images of type ' . get_class($image) . " is not possible with driver \"{$this->driver()}\"");
			
			return $this->load($image->get($format));
		}

		/**
		 * Gets suitable image formats for conversion of the given image
		 * @param ImageInterface $image The image
		 * @return string[] The image formats
		 */
		protected function conversionExportFormats(ImageInterface $image): array {
			
			// for vector images, we only can export as PNG
			if ($this->isVector($image))
				return ['svg'];
			
			
			// chose export format depending on driver
			foreach($this->drivers() as $currDriver) {
				$currImageClass = $this->driverImageClass($currDriver);
				if ($image instanceof $currImageClass)
					return $this->conversionDriverFormats(true, $currDriver);
			}

			throw new RuntimeException('Unknown image implementation "' . get_class($image) . '".');
		}

		/**
		 * Gets suitable image formats for conversion which the driver supports
		 * @param string|null $driver The driver name. If null, the current driver will be chosen.
		 * @return string[] The image formats
		 */
		protected function conversionDriverFormats(bool $export, string $driver = null): array {

			$driver = $driver ?: $this->driver();
			
			switch ($driver) {
				case self::DRIVER_GD:
					return ['png'];
					
				case self::DRIVER_GMAGICK:
				case self::DRIVER_IMAGICK:
				case self::DRIVER_VIPS:
					if ($export) {
						return ['tiff', 'png'];
					} else {
						return ['svg', 'tiff', 'png'];
					}

				default:
					throw new RuntimeException("Unknown driver \"{$driver}\".");
			}
			
		}

		/**
		 * Checks if the given image as a vector image
		 * @param ImageInterface $image The image
		 * @return bool True if is a vector image. Else false.
		 */
		protected function isVector(ImageInterface $image): bool {
			return ($image instanceof Image);
		}

		/**
		 * Tries to create a given imagine class
		 * @param string $class The class
		 * @return bool True if successful. Else false.
		 */
		protected function tryCreateImagine(string $class): bool {
			try {
				new $class();

				return true;
			}
			catch (\Imagine\Exception\RuntimeException $ex) {
				return false;
			}
		}

		/**
		 * Gets the image class for the given driver
		 * @param string|null $driver The driver name. If null, the current driver will be chosen.
		 * @return string
		 */
		protected function driverImageClass(string $driver = null): string {
			$driver = $driver ?: $this->driver();

			switch ($driver) {
				case self::DRIVER_GD:
					return 'Imagine\Gd\Image';
				case self::DRIVER_GMAGICK:
					return 'Imagine\Gmagick\Image';
				case self::DRIVER_IMAGICK:
					return 'Imagine\Imagick\Image';
				case self::DRIVER_VIPS:
					return 'Imagine\Vips\Image';

				default:
					throw new RuntimeException("Unknown driver \"{$driver}\".");
			}
		}

		/**
		 * Creates a new imagine instance.
		 * @return ImagineInterface
		 */
		public function imagine(): ImagineInterface {

			$driver = $this->driver();

			switch ($driver) {
				case self::DRIVER_GD:
					return new ImagineGd();
				case self::DRIVER_GMAGICK:
					return new ImagineGmagick();
				case self::DRIVER_IMAGICK:
					return new ImagineImagick();
				case self::DRIVER_VIPS:
					return new ImagineVips((array)($this->config['vips'] ?? []));

				default:
					throw new RuntimeException("Unknown driver \"{$driver}\" configured.");
			}
		}
	}