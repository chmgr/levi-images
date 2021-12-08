<?php

	namespace MehrIt\LeviImages\Raster\Filter;

	use Imagine\Filter\FilterInterface;
	use Imagine\Image\ImageInterface;
	use Imagine\Image\ImagineInterface;
	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Point;
	use InvalidArgumentException;

	/**
	 * Adds a background with the given color to the image. Optionally a margin can be applied to the image.
	 */
	class CanvasFilter implements FilterInterface
	{
		/**
		 * @var ImagineInterface 
		 */
		protected $imagine;

		/**
		 * @var ColorInterface
		 */
		protected $background;

		/**
		 * @var int The margin to add
		 */
		protected $margin;

		/**
		 * Creates a new instance
		 * @param ImagineInterface $imagine The imagine instance
		 * @param ColorInterface $background The background color
		 * @param int $margin The margin to add around the image
		 */
		public function __construct(ImagineInterface $imagine, ColorInterface $background, int $margin = 0) {

			if ($margin < 0)
				throw new InvalidArgumentException("Margin must not be negative but got {$margin}");
			
			$this->imagine    = $imagine;
			$this->background = $background;
			$this->margin     = $margin;
		}


		/**
		 * @inheritDoc
		 */
		public function apply(ImageInterface $image) {


			// create canvas
			$canvas = $this->imagine->create($image->getSize()->increase($this->margin * 2), $this->background);

			// paste image
			$canvas->paste($image, new Point($this->margin, $this->margin));


			return $canvas;
		}

	}