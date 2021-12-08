<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Raster\Filter;

	use Imagine\Image\Box;
	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Palette\Grayscale;
	use Imagine\Image\Palette\RGB;
	use Imagine\Image\Point;
	use MehrIt\LeviImages\Raster\Filter\PaletteFilter;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;

	class PaletteFilterTest extends TestCase
	{

		public function testApply_vips_rgb_rgb() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");
			
			
			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));
			
			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);
			
			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}
		
		public function testApply_vips_rgb_grayscale() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");
			
			
			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));
			
			$filter = new PaletteFilter(new Grayscale());

			$imgAfterFilter = $filter->apply($image);
			
			$this->assertInstanceOf(Grayscale::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[132, 100],
				[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
			);
		}

		public function testApply_vips_grayscale_rgb() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$image = $f->create(new Box(100, 100), (new Grayscale())->color('dddddd', 100));

			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);
			
			
			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[239, 239, 239, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}
		
		public function testApply_imagick_rgb_rgb() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");
			
			
			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));
			
			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);
			
			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}
		
		public function testApply_imagick_rgb_grayscale() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");
			
			
			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));
			
			$filter = new PaletteFilter(new Grayscale());

			$imgAfterFilter = $filter->apply($image);
			
			$this->assertInstanceOf(Grayscale::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[93, 100],
				[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
			);
		}

		public function testApply_imagick_grayscale_rgb() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$image = $f->create(new Box(100, 100), (new Grayscale())->color('dddddd', 100));

			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);
			
			
			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[231, 231, 231, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}

		public function testApply_gmagick_rgb_rgb() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));

			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);

			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}
		
		public function testApply_gmagick_rgb_grayscale() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");
			
			
			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));
			
			$filter = new PaletteFilter(new Grayscale());

			$imgAfterFilter = $filter->apply($image);
			
			$this->assertInstanceOf(Grayscale::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[93, 100],
				[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
			);
		}

		public function testApply_gmagick_grayscale_rgb() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$image = $f->create(new Box(100, 100), (new Grayscale())->color('dddddd', 100));

			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);
			
			
			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[231, 231, 231, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}

		public function testApply_gd_rgb_rgb() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$image = $f->create(new Box(100, 100), (new RGB())->color("ff0000"));

			$filter = new PaletteFilter(new RGB());

			$imgAfterFilter = $filter->apply($image);

			$this->assertInstanceOf(RGB::class, $image->palette());

			$color = $imgAfterFilter->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);
		}
		
	}