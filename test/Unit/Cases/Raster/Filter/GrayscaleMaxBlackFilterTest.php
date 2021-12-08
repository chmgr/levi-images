<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Raster\Filter;

	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Point;
	use MehrIt\LeviImages\Raster\Filter\GrayscaleMaxBlackFilter;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;

	class GrayscaleMaxBlackFilterTest extends TestCase
	{
		public function testApply_vips_oneColor() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('shape_one_color.png', function ($file) use ($f) {

				$img = $f->open($file);
				

				$filter = new GrayscaleMaxBlackFilter();

				$imgAfterFilter = $filter->apply($img);

				$imgAfterFilter->save($this->testOutputPath('png'));

				
				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(35, 10));
				$this->assertColorMatching(
					[0, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_vips_multiColor() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-white.png', function ($file) use ($f) {

				$img = $f->open($file);
				

				$filter = new GrayscaleMaxBlackFilter();

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255,  100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(40, 40));
				$this->assertColorMatching(
					[0, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(150, 40));
				$this->assertColorMatching(
					[95, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_vips_multiColorWithTrans() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-trans.png', function ($file) use ($f) {

				$img = $f->open($file);
				

				$filter = new GrayscaleMaxBlackFilter();

				$imgAfterFilter = $filter->apply($img);

				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[0],
					[$color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(40, 40));
				$this->assertColorMatching(
					[0, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(150, 40));
				$this->assertColorMatching(
					[95, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

			});

		}

		public function testApply_imagick_oneColor() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('shape_one_color.png', function ($file) use ($f) {

				$img = $f->open($file);


				$filter = new GrayscaleMaxBlackFilter();

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(35, 10));
				$this->assertColorMatching(
					[0, 0, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_imagick_multiColor() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-white.png', function ($file) use ($f) {

				$img = $f->open($file);


				$filter = new GrayscaleMaxBlackFilter();

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(40, 40));
				$this->assertColorMatching(
					[0, 0, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(150, 40));
				$this->assertColorMatching(
					[35, 35, 35, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
			});

		}
		
		public function testApply_imagick_multiColorWithTransparency() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-trans.png', function ($file) use ($f) {

				$img = $f->open($file);


				$filter = new GrayscaleMaxBlackFilter();

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[0],
					[$color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(40, 40));
				$this->assertColorMatching(
					[0, 0, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(150, 40));
				$this->assertColorMatching(
					[35, 35, 35, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

	}