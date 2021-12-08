<?php

	namespace MehrIt\LeviImages\Raster\Filter;

	use Imagine\Filter\FilterInterface;
	use Imagine\Image\ImageInterface;
	use Imagine\Image\Palette\PaletteInterface;
	use Imagine\Vips\Image as VipsImage;
	use Jcupitt\Vips\BandFormat;

	/**
	 * This filter converts the image to the given palette. For vips it also ensures bandformat UCHAR or USHORT.
	 */
	class PaletteFilter implements FilterInterface
	{
		/**
		 * @var PaletteInterface
		 */
		protected $palette;

		/**
		 * @param PaletteInterface $palette
		 */
		public function __construct(PaletteInterface $palette) {
			$this->palette = $palette;
		}


		/**
		 * @inheritDoc
		 */
		public function apply(ImageInterface $image) {

			
			// change palette
			if ($image->palette()->name() != $this->palette->name())
				$image->usePalette($this->palette);
			
			
			// for vips we also ensure UCHAR or USHORT to get rid of uncommon formats
			$vipsImageCls = '\Imagine\Vips\Image';
			if ($image instanceof $vipsImageCls) {
				
				/** @var VipsImage $image */
				$vipsImage = $image->getVips();
				
				if (!in_array($vipsImage->format, [BandFormat::UCHAR, BandFormat::USHORT]))
					$image->setVips($vipsImage->cast(BandFormat::UCHAR));
				
			}
			
			return $image;
		}


	}