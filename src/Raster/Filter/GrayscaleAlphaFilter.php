<?php

	namespace MehrIt\LeviImages\Raster\Filter;

	use Imagine\Filter\FilterInterface;
	use Imagine\Image\ImageInterface;
	use Imagine\Imagick\Image as ImagickImage;

	/**
	 * Converts the image to grayscale. This filter fixes destroyed alpha channels for imagick. 
	 */
	class GrayscaleAlphaFilter implements FilterInterface
	{
		/**
		 * @inheritDoc
		 */
		public function apply(ImageInterface $image) {
			
			// FIX: Imagine grayscale effect destroys the alpha channel for imagick.
			if ($image instanceof ImagickImage) {
				
				$image->getImagick()->setImageType(\Imagick::IMGTYPE_GRAYSCALEMATTE);
				
			} else {
				$image->effects()->grayscale();
			}
			
			return $image;
		}


	}