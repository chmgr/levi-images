<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Raster\Filter;

	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Palette\RGB;
	use Imagine\Image\Point;
	use MehrIt\LeviImages\Raster\Filter\CanvasFilter;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;


	class CanvasFilterTest extends TestCase
	{
		public function testApply_vips_withMargin() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());

				$color = $imgAfterFilter->getColorAt(new Point(15, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 15));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 30));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				

			});

		}
		
		public function testApply_vips_alphaWithMargin() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 15));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				

			});

		}
		
		public function testApply_vips_alphaWithoutMargin() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background);

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth(), $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight(), $imgAfterFilter->getSize()->getHeight());
				
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_imagick_withMargin() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());

				$color = $imgAfterFilter->getColorAt(new Point(15, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 15));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 30));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_imagick_alphaWithMargin() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 15));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_imagick_alphaWithoutMargin() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(),$background);

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth(), $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight(), $imgAfterFilter->getSize()->getHeight());
				
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				

			});

		}
		
		public function testApply_gmagick_withMargin() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());

				$color = $imgAfterFilter->getColorAt(new Point(15, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 15));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 30));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_gmagick_alphaWithMargin() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 15));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_gmagick_alphaWithoutMargin() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background);

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth(), $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight(), $imgAfterFilter->getSize()->getHeight());
				
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_gd_withMargin() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());

				$color = $imgAfterFilter->getColorAt(new Point(15, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 15));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(15, 30));
				$this->assertColorMatching(
					[128, 128, 128, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_gd_alphaWithMargin() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background, 15);

				$imgAfterFilter = $filter->apply($img);


				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth() + 30, $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight() + 30, $imgAfterFilter->getSize()->getHeight());
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 7));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 14));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 15));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}
		
		public function testApply_gd_alphaWithoutMargin() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function ($file) use ($f) {

				$img = $f->open($file);
				
				$origSize = $img->getSize();

				$background = (new RGB())->color("ffffff");

				$filter = new CanvasFilter($f->imagine(), $background);

				$imgAfterFilter = $filter->apply($img);
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertSame($origSize->getWidth(), $imgAfterFilter->getSize()->getWidth());
				$this->assertSame($origSize->getHeight(), $imgAfterFilter->getSize()->getHeight());
				
				
				$color = $imgAfterFilter->getColorAt(new Point(1000, 0));
				$this->assertColorMatching(
					[99, 76, 149, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $imgAfterFilter->getColorAt(new Point(100, 100));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		
	}