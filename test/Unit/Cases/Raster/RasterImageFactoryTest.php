<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Raster;

	use Contao\ImagineSvg\Image;
	use Contao\ImagineSvg\Imagine;
	use Imagine\Gd\Image as GdImage;
	use Imagine\Gmagick\Image as GmagickImage;
	use Imagine\Image\Box;
	use Imagine\Image\Palette\Color\ColorInterface;
	use Imagine\Image\Palette\RGB;
	use Imagine\Image\Point;
	use Imagine\Imagick\Image as ImagickImage;
	use Imagine\Vips\Image as VipsImage;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;
	use RuntimeException;

	class RasterImageFactoryTest extends TestCase
	{
		protected function openVectorImage(string $file): Image {

			return (new Imagine())->open($file);

		}

		protected function supportedDrivers(): array {
			$f = new RasterImageFactory();

			$supportedDrivers = [];
			foreach ($f->drivers() as $curr) {
				if ($f->isDriverSupported($curr))
					$supportedDrivers[] = $curr;
			}

			return $supportedDrivers;
		}

		public function testDrivers() {

			$f = new RasterImageFactory();

			$this->assertTrue(in_array(RasterImageFactory::DRIVER_GD, $f->drivers()));
			$this->assertTrue(in_array(RasterImageFactory::DRIVER_GMAGICK, $f->drivers()));
			$this->assertTrue(in_array(RasterImageFactory::DRIVER_IMAGICK, $f->drivers()));
			$this->assertTrue(in_array(RasterImageFactory::DRIVER_VIPS, $f->drivers()));

		}

		public function testIsDriverSupported() {

			$f = new RasterImageFactory();

			$this->expectNotToPerformAssertions();
			dump($f->isDriverSupported(RasterImageFactory::DRIVER_VIPS));
			$anySupported = false;
			if ($f->isDriverSupported(RasterImageFactory::DRIVER_VIPS))
				$anySupported = true;
			if ($f->isDriverSupported(RasterImageFactory::DRIVER_IMAGICK))
				$anySupported = true;
			if ($f->isDriverSupported(RasterImageFactory::DRIVER_GMAGICK))
				$anySupported = true;
			if ($f->isDriverSupported(RasterImageFactory::DRIVER_GD))
				$anySupported = true;

			if (!$anySupported)
				$this->fail('No driver is supported');
		}

		public function testDriver_explicitlyConfigured() {

			$supportedDrivers = $this->supportedDrivers();
			if (count($supportedDrivers) < 2)
				$this->markTestSkipped('At least two supported drivers are required for this test');


			foreach ($supportedDrivers as $currDriver) {
				$this->assertSame($currDriver, (new RasterImageFactory(['driver' => $currDriver]))->driver());
			}
		}

		public function testDriver_autoExplicitlyConfigured() {

			$supportedDrivers = $this->supportedDrivers();
			if (count($supportedDrivers) < 1)
				$this->markTestSkipped('At least one supported driver is required for this test');

			$this->assertNotEmpty((new RasterImageFactory(['driver' => 'auto']))->driver());

		}

		public function testDriver_notConfigured() {
			$supportedDrivers = $this->supportedDrivers();

			if (count($supportedDrivers) < 1)
				$this->markTestSkipped('At least one supported driver is required for this test');

			$this->assertNotEmpty((new RasterImageFactory())->driver());

		}

		public function testOpen_vips_png() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_vips_jpeg() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

			});

		}

		public function testOpen_vips_webp() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

			});

		}

		public function testOpen_vips_gif() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_vips_svg() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}


		public function testOpen_imagick_png() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_imagick_jpeg() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_imagick_webp() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_imagick_gif() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_imagick_svg() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gmagick_png() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gmagick_jpeg() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gmagick_webp() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gmagick_gif() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gmagick_svg() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gd_png() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gd_jpeg() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gd_webp() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testOpen_gd_gif() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->open($file);

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_vips_png() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_vips_jpeg() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

			});

		}

		public function testLoad_vips_webp() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

			});

		}

		public function testLoad_vips_gif() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_vips_svg() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}


		public function testLoad_imagick_png() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_imagick_jpeg() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_imagick_webp() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_imagick_gif() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_imagick_svg() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gmagick_png() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gmagick_jpeg() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gmagick_webp() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gmagick_gif() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gmagick_svg() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gd_png() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gd_jpeg() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gd_webp() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testLoad_gd_gif() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->load(file_get_contents($file));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_vips_png() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_vips_jpeg() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

			});

		}

		public function testRead_vips_webp() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE)]
				);

			});

		}

		public function testRead_vips_gif() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_vips_svg() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}


		public function testRead_imagick_png() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_imagick_jpeg() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_imagick_webp() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_imagick_gif() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_imagick_svg() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gmagick_png() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gmagick_jpeg() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gmagick_webp() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gmagick_gif() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gmagick_svg() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gd_png() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gd_jpeg() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.jpg', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gd_webp() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.webp', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testRead_gd_gif() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.gif', function ($file) use ($f) {

				$img = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}


		public function testCreate_vips() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$img = $f->create(new Box(100, 100), (new RGB())->color("ff0000", 100));

			$this->assertInstanceOf(VipsImage::class, $img);

			$color = $img->getColorAt(new Point(0, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(0, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

		}

		public function testCreate_imagick() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$img = $f->create(new Box(100, 100), (new RGB())->color("ff0000", 100));

			$this->assertInstanceOf(ImagickImage::class, $img);

			$color = $img->getColorAt(new Point(0, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(0, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

		}

		public function testCreate_gmagick() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$img = $f->create(new Box(100, 100), (new RGB())->color("ff0000", 100));

			$this->assertInstanceOf(GmagickImage::class, $img);

			$color = $img->getColorAt(new Point(0, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(0, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

		}

		public function testCreate_gd() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$img = $f->create(new Box(100, 100), (new RGB())->color("ff0000", 100));

			$this->assertInstanceOf(GdImage::class, $img);

			$color = $img->getColorAt(new Point(0, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(0, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 99));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(99, 0));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

			$color = $img->getColorAt(new Point(50, 50));
			$this->assertColorMatching(
				[255, 0, 0, 100],
				[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
			);

		}


		public function testImport_vips_vector() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);

				$img = $f->import($vector);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_vips_vectorResized() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);
				$vector->resize(new Box(542 * 2, 542 * 2));

				$img = $f->import($vector);

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(180, 280));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(480, 280));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_vips_fromOtherDriver() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$sourceFactory = null;
				foreach ($this->supportedDrivers() as $currDriver) {
					if ($currDriver != $driver) {
						$sourceFactory = new RasterImageFactory(['driver' => $currDriver]);
						break;
					}
				}
				if (!$sourceFactory)
					$this->markTestSkipped("No other driver than {$driver} is supported. But an additional driver is required for this test.");

				$img = $f->import($sourceFactory->open($file));


				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_vips_fromVips() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$img = $f->import($f->open($file));

				$this->assertInstanceOf(VipsImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_imagick_vector() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);

				$img = $f->import($vector);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_imagick_vectorResized() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);
				$vector->resize(new Box(542 * 2, 542 * 2));

				$img = $f->import($vector);

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(180, 280));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(480, 280));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_imagick_fromOtherDriver() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$sourceFactory = null;
				foreach ($this->supportedDrivers() as $currDriver) {
					if ($currDriver != $driver) {
						$sourceFactory = new RasterImageFactory(['driver' => $currDriver]);
						break;
					}
				}
				if (!$sourceFactory)
					$this->markTestSkipped("No other driver than {$driver} is supported. But an additional driver is required for this test.");

				$img = $f->import($sourceFactory->open($file));


				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_imagick_fromImagick() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$img = $f->import($f->open($file));

				$this->assertInstanceOf(ImagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_gmagick_vector() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);

				$img = $f->import($vector);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_gmagick_vectorResized() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);
				$vector->resize(new Box(542 * 2, 542 * 2));

				$img = $f->import($vector);

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(180, 280));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(480, 280));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_gmagick_fromOtherDriver() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$sourceFactory = null;
				foreach ($this->supportedDrivers() as $currDriver) {
					if ($currDriver != $driver) {
						$sourceFactory = new RasterImageFactory(['driver' => $currDriver]);
						break;
					}
				}
				if (!$sourceFactory)
					$this->markTestSkipped("No other driver than {$driver} is supported. But an additional driver is required for this test.");

				$img = $f->import($sourceFactory->open($file));


				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_gmagick_fromGmagick() {

			$driver = RasterImageFactory::DRIVER_GMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$img = $f->import($f->open($file));

				$this->assertInstanceOf(GmagickImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_gd_vector() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.svg', function ($file) use ($f) {

				$vector = $this->openVectorImage($file);

				$this->expectException(RuntimeException::class);
				$this->expectErrorMessageMatches('/Importing images of type/');
				$f->import($vector);

			});

		}

		public function testImport_gd_fromOtherDriver() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$sourceFactory = null;
				foreach ($this->supportedDrivers() as $currDriver) {
					if ($currDriver != $driver) {
						$sourceFactory = new RasterImageFactory(['driver' => $currDriver]);
						break;
					}
				}
				if (!$sourceFactory)
					$this->markTestSkipped("No other driver than {$driver} is supported. But an additional driver is required for this test.");

				$img = $f->import($sourceFactory->open($file));


				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}

		public function testImport_gd_fromGd() {

			$driver = RasterImageFactory::DRIVER_GD;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('test.png', function ($file) use ($f, $driver) {

				$img = $f->import($f->open($file));

				$this->assertInstanceOf(GdImage::class, $img);

				$color = $img->getColorAt(new Point(90, 140));
				$this->assertColorMatching(
					[255, 255, 255, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

				$color = $img->getColorAt(new Point(240, 140));
				$this->assertColorMatching(
					[0, 255, 0, 100],
					[$color->getValue(ColorInterface::COLOR_RED), $color->getValue(ColorInterface::COLOR_GREEN), $color->getValue(ColorInterface::COLOR_BLUE), $color->getAlpha()]
				);

			});

		}


		public function testUseDriver() {

			$drivers = $this->supportedDrivers();

			if (count($drivers) < 2)
				$this->markTestSkipped('At least 2 supported drivers are required for this test.');


			$f1 = new RasterImageFactory(['driver' => $drivers[0]]);

			$f2 = $f1->useDriver($drivers[1]);

			$this->assertNotSame($f1, $f2);
			$this->assertSame($drivers[0], $f1->driver());
			$this->assertSame($drivers[1], $f2->driver());

		}


	}