<?php

	namespace MehrIt\LeviImages\Raster\Filter;

	use Imagine\Filter\FilterInterface;
	use Imagine\Image\ImageInterface;
	use Imagine\Image\Metadata\MetadataBag;
	use Imagine\Imagick\Image as ImagickImage;
	use Imagine\Vips\Image as VipsImage;
	use Imagine\Vips\Imagine;
	use Jcupitt\Vips\BandFormat;
	use Jcupitt\Vips\Interpretation;

	/**
	 * Applies the GrayscaleAlpha filter and maximizes the "black" value. The darkest color will become black. This filter works only
	 * for imagine and vips driver (vips only for (s)RGB, GRAY16 and B_W if band format is UCHAR or USHORT)
	 */
	class GrayscaleMaxBlackFilter implements FilterInterface
	{

		/**
		 * @var float 
		 */
		protected $threshold;

		/**
		 * Creates a new instance.
		 * @param float $threshold Percentage of pixels to ignore when determining the darkest color. E.g. 0.02 means that
		 * up to 2% of the darkest pixels are ignored. This prevents adjusting the max black value according to a little minority 
		 * of pixels hardly visible.
		 */
		public function __construct(float $threshold = 0.02) {
			$this->threshold = $threshold;
		}


		/**
		 * @inheritDoc
		 */
		public function apply(ImageInterface $image) {
			
			// convert to grayscale
			$image = (new GrayscaleAlphaFilter())->apply($image);

			$vipsImageCls = '\Imagine\Vips\Image';

			if ($image instanceof $vipsImageCls) {
				// vips
				
				/** @var VipsImage $image */
				$vipsImage = $image->getVips();
				
				switch($vipsImage->interpretation) {
					case Interpretation::SRGB:
					case Interpretation::RGB:
					case Interpretation::GREY16:
					case Interpretation::B_W:
						switch($vipsImage->format) {
							case BandFormat::UCHAR:
								$white = 255;
								break;
							case BandFormat::USHORT:
								$white = 65535;
								break;
							default:
								// ignore filter for other image formats
								return $image;
						}
						
						break;
						
					default:
						// ignore filter for other image formats
						return $image;
				}

				$nonAlphaBands = $vipsImage->bands - ($vipsImage->hasAlpha() ? 1 : 0);

				// Flatten the image to white background. We need this so that values of transparent pixels are
				// not taken into account when calculating the histogram.
				$vipsImageFlattened = $vipsImage->flatten([
					'background' => array_fill(0, $nonAlphaBands, $white)
				]);

				// create a lookup table from the image grayscale histogram
				$lut = $this->createLut(
					$vipsImageFlattened->hist_find(['band' => 0])->writeToArray(),
					(int)($vipsImage->width * $vipsImage->height * $this->threshold)
				);
				
				// apply the lookup table to all bands (except alpha)
				for($band = 0; $band <= $nonAlphaBands; ++$band) {
					$vipsImage = $vipsImage->maplut(\Jcupitt\Vips\Image::newFromArray($lut), ['band' => $band]);
				}
				
				$image = new VipsImage($vipsImage, Imagine::createPalette($vipsImage), new MetadataBag());
			}
			elseif ($image instanceof ImagickImage) {
				// imagick

				$imImage = $image->getImagick();
				
				$histSource = (new \Imagick());
				try {
					// Flatten the image to white background. We need this so that values of transparent pixels are
					// not taken into account when calculating the histogram.
					$histSource->newImage($imImage->getImageWidth(), $imImage->getImageHeight(), new \ImagickPixel('white'));
					$histSource->compositeImage($imImage, \Imagick::COMPOSITE_DEFAULT, 0, 0);

					// build a grayscale histogram
					$hist = array_fill(0, 255, 0);
					foreach ($histSource->getImageHistogram() as $currHistElement) {
						/** @var \ImagickPixel $currHistElement */
						// for grayscale, alle channels should have the same value, so we simply pick "red"
						$color = $currHistElement->getColorValue(\Imagick::COLOR_RED);
						$color = intval($color * 255);
						$count = $currHistElement->getColorCount();

						$hist[$color] = $count;
					}
				}
				finally {
					$histSource->clear();
				}
				
				// create a lookup table (as array)
				$lut = $this->createLut($hist, $imImage->getImageWidth() * $imImage->getImageHeight() * 0.02);
				
				// create a lookup table image
				try {
					$lutImage = new \Imagick();
					$lutImage->newImage(256, 1, new \ImagickPixel('red'));
					$iter = $lutImage->getPixelIterator();
					foreach ($iter as $pixels) {
						foreach ($pixels as $i => $currPixel) {
							/** @var \ImagickPixel $currPixel */
							$currPixel->setColor("rgb({$lut[$i]},{$lut[$i]},{$lut[$i]})");
						}
						$iter->syncIterator();
					}


					// apply the lookup table
					$imImage->clutImage($lutImage, \Imagick::CHANNEL_RED | \Imagick::CHANNEL_GREEN | \Imagick::CHANNEL_BLUE);
				}
				finally {
					$lutImage->clear();
				}

			}
			


			return $image;
		}



		protected function createLut(array $hist, int $threshold) {
			$histSize = count($hist);
			$maxColorValue = ($histSize - 1);

			$min        = 0;
			$pixelsSeen = 0;
			foreach ($hist as $index => $currCount) {
				$pixelsSeen += $currCount;

				if ($pixelsSeen < $threshold)
					continue;

				if ($currCount > 0) {
					$min = $index;
					break;
				}
			}

			$range = $maxColorValue - $min ?: 1;

			$lut = [];
			for ($i = 0; $i <= $histSize; ++$i) {
				$lut[$i] = (int)max(0, (($i - $min) / $range) * $maxColorValue);
			}

			return $lut;
		}

	}