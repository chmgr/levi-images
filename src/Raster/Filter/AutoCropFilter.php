<?php

	namespace MehrIt\LeviImages\Raster\Filter;

	use Imagine\Filter\FilterInterface;
	use Imagine\Image\Box;
	use Imagine\Image\ImageInterface;
	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Palette\RGB;
	use Imagine\Image\Point;
	use Imagine\Imagick\Image as ImagickImage;
	use Imagine\Vips\Image as VipsImage;
	use Imagine\Vips\Imagine as VipsImagine;
	use InvalidArgumentException;

	/**
	 * Automatically crops the image background color. This filter only works for 'vips' and 'imagick' implementation. Does nothing
	 * for any other implementation. 
	 */
	class AutoCropFilter implements FilterInterface
	{

		/**
		 * @var float
		 */
		protected $sensitivity;

		/**
		 * @var ColorInterface
		 */
		protected $background;

		/**
		 * Creates a new instance
		 * @param ColorInterface $background The background color
		 * @param float $sensitivity The color sensitivity (0.0 - 1.0)
		 */
		public function __construct(ColorInterface $background, float $sensitivity) {

			if (!$background->isOpaque())
				throw new InvalidArgumentException("Color must be opaque, but alpha is {$background->getAlpha()} ($background)");

			$palette = $background->getPalette();
			if (!($palette instanceof RGB))
				throw new InvalidArgumentException('Color must use RGB palette');

			$this->sensitivity = $sensitivity;
			$this->background  = $background;
		}


		/**
		 * @inheritDoc
		 */
		public function apply(ImageInterface $image) {

			$vipsImageCls    = '\Imagine\Vips\Image';

			if ($image instanceof $vipsImageCls) {
				// use libvips

				// image must be at least 3x3 pixel
				if ($image->getSize()->getWidth() >= 3 && $image->getSize()->getHeight() >= 3) {

					$im = new VipsImagine();


					// Create an image we can analyze. We Create a new RGB image with our background color
					// and paste the target image. That ensures that all transparent areas have our background
					// color.
					$analyzeImage = $im->create($image->getSize(), $this->background);
					$analyzeImage->paste($image, new Point(0, 0));

					// find our crop bounds
					$trim = $analyzeImage->getVips()->find_trim([
						'threshold'  => (int)(255 * $this->sensitivity),
						'background' => array_slice(VipsImage::getColorArrayAlpha($this->background), 0, 3)
					]);


					// only crop, if s.th. is left
					if ($trim['width'] > 0 && $trim['height'] > 0 && $trim['left'] >= 0 && $trim['top'] >= 0)
						$image->crop(new Point($trim['left'], $trim['top']), new Box($trim['width'], $trim['height']));
				}

			}

			else if ($image instanceof ImagickImage) {
				// use imagick

				$origSize = $image->getSize();

				try {
					// Create an image we can crop. We Create a new RGB image with our background color and 5px of border
					// and paste the target image. That ensures that all transparent areas have our background
					// color and edges have bg color.
					$cropImage = new \Imagick();
					$cropImage->newImage($origSize->getWidth() + 10, $origSize->getHeight() + 10, '#ffffff');
					$cropImage->compositeImage($image->getImagick(), \Imagick::COMPOSITE_DEFAULT, 5, 5);

					// perform imagine trimImage
					/** @var \Imagick $cropImage */
					$cropImage
						->trimImage($this->sensitivity * \Imagick::getQuantumRange()['quantumRangeLong']);

					// We need the image page coordinates to get the cropped area start. Note: width and height of the image page
					// do not change on trimImage, we need to use getImageWidth() to get trimmed width.
					$page          = $cropImage->getImagePage();
					$trimmedWidth  = $cropImage->getImageWidth();
					$trimmedHeight = $cropImage->getImageHeight();


					if ($trimmedWidth > 0 && $trimmedHeight > 0 && $page['x'] >= 0 && $page['y'] >= 0 && ($trimmedWidth < $origSize->getWidth() || $trimmedHeight < $origSize->getHeight()))
						$image->crop(new Point($page['x'], $page['y']), new Box($trimmedWidth, $trimmedHeight));
				}
				finally {
					if (!empty($cropImage))
						$cropImage->destroy();
				}

			}
			

			return $image;
		}

	}