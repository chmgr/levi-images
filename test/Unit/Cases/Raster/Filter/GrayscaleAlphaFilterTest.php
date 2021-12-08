<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Raster\Filter;

	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Point;
	use MehrIt\LeviImages\Raster\Filter\GrayscaleAlphaFilter;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;

	class GrayscaleAlphaFilterTest extends TestCase
	{

		public function testApply_vips_alpha() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-trans.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);

				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[0],
					[$color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[45, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}

		public function testApply_vips_opaque() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-white.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[45, 100],
					[$color->getValue(ColorInterface::COLOR_GRAY), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}
		
		public function testApply_imagick_alpha() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-trans.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);

				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[0],
					[$color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[25, 25, 25, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}

		public function testApply_imagick_opaque() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-white.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[25, 25, 25, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}
		
		public function testApply_gmagick_alpha() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-trans.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);

				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[0],
					[$color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[25, 25, 25, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}

		public function testApply_gmagick_opaque() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-white.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[25, 25, 25, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}
		
		public function testApply_gd_alpha() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-trans.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);

				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[0],
					[$color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[25, 25, 25, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}

		public function testApply_gd_opaque() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('color-test-white.png', function ($file) use ($f) {

				$img = $f->open($file);

				$filter = new GrayscaleAlphaFilter();

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$color = $imgAfterFilter->getColorAt(new Point(5, 5));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(30, 30));
				$this->assertColorMatching(
					[25, 25, 25, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$this->assertSame($img, $imgAfterFilter);

			});

		}

	}