<?php

	namespace MehrIt\LeviImages\Optimization;

	use Illuminate\Support\Arr;
	use Imagine\Image\ImageInterface;
	use InvalidArgumentException;
	use MehrIt\LeviImages\Util\TemporaryFiles;
	use RuntimeException;
	use Spatie\ImageOptimizer\OptimizerChain;
	use Spatie\ImageOptimizer\OptimizerChainFactory;

	class Optimizer
	{
		use TemporaryFiles;
		
		const FORMAT_SVG = 'svg';
		const FORMAT_PNG = 'png';
		const FORMAT_JPEG = 'jpeg';
		const FORMAT_WEBP = 'webp';
		const FORMAT_GIF= 'gif';

		/**
		 * @var callable
		 */
		protected $optimizersResolver;

		/**
		 * @var int
		 */
		protected $timeout = 60;

		/**
		 * @param array $config
		 */
		public function __construct(array $config = []) {
			$this->timeout = ($config['timeout'] ?? 60) ?: 60;
		}


		/**
		 * Sets a callback which resolves the optimizers to use
		 * @param callable $resolver The resolver. Must return an array of \Spatie\ImageOptimizer\Optimizer.
		 * @return $this
		 */
		public function setOptimizersResolver(callable $resolver): Optimizer {
			$this->optimizersResolver = $resolver;
			
			return $this;
		}

		/**
		 * Sets the maximum of time in seconds that each individual optimizer in a chain can use
		 * @param int $timeout The timeout
		 * @return Optimizer
		 */
		public function setTimeout(int $timeout): Optimizer {
			$this->timeout = $timeout;

			return $this;
		}

		/**
		 * Gets the maximum of time in seconds that each individual optimizer in a chain can use
		 * @return int The maximum of time in seconds that each individual optimizer in a chain can use
		 */
		public function getTimeout(): int {
			return $this->timeout;
		}

		/**
		 * Gets the optimizers
		 * @return \Spatie\ImageOptimizer\Optimizer[] The optimizers
		 */
		public function getOptimizers(): array {
			return $this->chain()->getOptimizers();
		}
		
		/**
		 * Optimizes the given file
		 * @param string $path The path
		 * @param string|null $outputPath If given, the output is saved to the output path instead of manipulating the existing file.
		 */
		public function optimizeFile(string $path, string $outputPath = null): Optimizer {

			$isSvg = in_array(mime_content_type($path), [
				'text/html',
				'image/svg',
				'image/svg+xml',
				'text/plain',
			]);

			if ($isSvg) {
				// Special handling for SVG is required because SVGO ignores files without ".svg" extension.
				// resource() will handle this
				$inRes = fopen($path, 'r');
				try {
					if ($inRes === false)
						throw new RuntimeException("Failed to open file \"$path\"");

					$outRes = $this->optimizeResource($inRes);

					file_put_contents($outputPath ?: $path, $outRes);

				}
				finally {
					if (is_resource($inRes))
						fclose($inRes);
					if (!empty($outRes) && is_resource($outRes))
						fclose($outRes);
				}
				
			} else {
				
				$this->optimizeFileToFile($path, $outputPath);
			}
			
			
			
			return $this;
		}

		/**
		 * Optimizes the given image resource
		 * @param resource $resource The resource
		 * @return resource A new resource with optimized the image
		 */
		public function optimizeResource($resource) {

			$firstBytes = fread($resource, 100);
			
			// We need to detect SVGs here. This is because SVGO requires files to have .svg extension.
			$isSvg = (strpos($firstBytes, '<?xml') !== false) || (strpos($firstBytes, '<svg') !== false);
			
			return $this->withTempFile(function ($tempFile) use ($firstBytes, $resource) {
				
				file_put_contents($tempFile, $firstBytes);
				file_put_contents($tempFile, $resource, FILE_APPEND);
				
				return $this->optimizeAsResource($tempFile);
				
			}, null, $isSvg ? 'svg' : null /* use .svg as required by SVGO */);
		}

		/**
		 * Optimizes the given image
		 * @param ImageInterface $image The image
		 * @param string $format The format
		 * @param array $saveOptions The options to save the image before optimizing.
		 * @return resource A resource with optimized the image
		 */
		public function optimizeImage(ImageInterface $image, string $format, array $saveOptions = []) {
			
			return $this->withTempFile(function($tempFile) use ($image, $format, $saveOptions) {
				
				$image = $image->strip();
				
				switch ($format) {
					case self::FORMAT_JPEG:
					case self::FORMAT_PNG:
					case self::FORMAT_WEBP:
					case self::FORMAT_GIF:
					case self::FORMAT_SVG:
						$image->save($tempFile, $saveOptions);
						
						break;
						
					default:
						throw new InvalidArgumentException("Invalid format \"{$format}\" for optimizing raster image");
				}

				return $this->optimizeAsResource($tempFile);	
				
			}, null, $format);
			
		}

		/**
		 * Optimizes the given temporary file and returns a resource 
		 * @param string $temporaryFile The temporary file
		 * @return resource The resource
		 */
		protected function optimizeAsResource(string $temporaryFile) {
			$this->optimizeFileToFile($temporaryFile);

			// copy to a resource which is deleted on close
			$ret = tmpfile();
			if (!$ret)
				throw new RuntimeException("Failed to create a temporary file.");
			
			try {
				$inRes = fopen($temporaryFile, 'r');
				if ($inRes === false)
					throw new RuntimeException("Failed to open temporary file.");
				if (!stream_copy_to_stream($inRes, $ret))
					throw new RuntimeException("Failed to write to temporary file.");
			}
			finally {
				if (is_resource($inRes))
					fclose($inRes);
			}
			
			if (!rewind($ret))
				throw new RuntimeException("Failed rewind temporary file.");
			
			return $ret;
		}


		/**
		 * Optimizes the given file
		 * @param string $filename The filename
		 * @param string|null $outputPath Optional path for separate output path.
		 */
		protected function optimizeFileToFile(string $filename, string $outputPath = null) {

			$this->chain()->optimize($filename, $outputPath);
		}

		/**
		 * Creates an optimizer chain
		 * @return OptimizerChain
		 */
		protected function chain(): OptimizerChain {
			
			if ($this->optimizersResolver) {
				
				$optimizers = call_user_func($this->optimizersResolver);

				$chain = new OptimizerChain();
								
				foreach(Arr::wrap($optimizers) as $index => $currOptimizer) {
					if (!($currOptimizer instanceof \Spatie\ImageOptimizer\Optimizer))
						throw new RuntimeException('The optimizers resolver must return only ' . \Spatie\ImageOptimizer\Optimizer::class . ', got ' . (is_object($currOptimizer) ? get_class($currOptimizer) : strtolower(gettype($currOptimizer))) . ' at index ' . $index);
					
					$chain->addOptimizer($currOptimizer);
				}			
			}
			else {
				$chain = OptimizerChainFactory::create();
			}

			$chain->setTimeout($this->timeout);
			
			return $chain;
		}
	}