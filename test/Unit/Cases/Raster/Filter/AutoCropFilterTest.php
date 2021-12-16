<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Raster\Filter;

	
	use Imagine\Image\Palette\CMYK;
	use Imagine\Image\Palette\RGB;
	use InvalidArgumentException;
	use MehrIt\LeviImages\Raster\Filter\AutoCropFilter;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;

	class AutoCropFilterTest extends TestCase
	{

		public function testApply_vips_alpha() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function($file) use ($f) {
				
				$img = $f->open($file);

				$background = (new RGB())->color("ffffff");

				$filter = new AutoCropFilter($background, 0.01);
				
				$imgAfterFilter = $filter->apply($img);				
				
				
				$imgAfterFilter->save($this->testOutputPath('png'));

				$this->assertInTolerance(1758, $imgAfterFilter->getSize()->getWidth(), 50);
				$this->assertInTolerance(1256, $imgAfterFilter->getSize()->getHeight(), 50);
				
				$this->assertSame($img, $imgAfterFilter);
				
			});
			
		}
		
		public function testApply_vips_gradient() {

			$driver = RasterImageFactory::DRIVER_VIPS;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_gradient.jpg', function($file) use ($f) {


				$img = $f->open($file);

				$background = (new RGB())->color("ffffff");

				$filter = new AutoCropFilter($background, 0.01);

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath('jpg'));

				$this->assertInTolerance(880, $imgAfterFilter->getSize()->getWidth(),  50);
				$this->assertInTolerance(832, $imgAfterFilter->getSize()->getHeight(),  50);

				$this->assertSame($img, $imgAfterFilter);
				
				
			});
			
		}
		
		public function testApply_imagick_alpha() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_alpha.png', function($file) use ($f) {
				
				$img = $f->open($file);

				$background = (new RGB())->color("ffffff");

				$filter = new AutoCropFilter($background, 0.01);
				
				$imgAfterFilter = $filter->apply($img);				
				
				
				$imgAfterFilter->save($this->testOutputPath( 'png'));

				$this->assertInTolerance(1758, $imgAfterFilter->getSize()->getWidth(), 50);
				$this->assertInTolerance(1256, $imgAfterFilter->getSize()->getHeight(), 50);
				
				$this->assertSame($img, $imgAfterFilter);
				
			});
			
		}
		
		public function testApply_imagick_gradient() {

			$driver = RasterImageFactory::DRIVER_IMAGICK;

			$f = new RasterImageFactory(['driver' => $driver]);
			if (!$f->isDriverSupported($driver))
				$this->markTestSkipped("Driver {$driver} is not supported");


			$this->withTempTestImage('auto_crop_gradient.jpg', function($file) use ($f) {


				$img = $f->open($file);

				$background = (new RGB())->color("ffffff");

				$filter = new AutoCropFilter($background, 0.01);

				$imgAfterFilter = $filter->apply($img);


				$imgAfterFilter->save($this->testOutputPath( 'jpg'));

				$this->assertInTolerance(880, $imgAfterFilter->getSize()->getWidth(), 50);
				$this->assertInTolerance(832, $imgAfterFilter->getSize()->getHeight(), 50);

				$this->assertSame($img, $imgAfterFilter);
				
				
			});
			
		}
		
		public function testConstructor_nonOpaque() {
			
			$this->expectException(InvalidArgumentException::class);
			$this->expectErrorMessageMatches('/opaque/i');
			
			new AutoCropFilter((new RGB())->color("ffffff", 50), 1);			
		}

		public function testConstructor_nonRgb() {

			$this->expectException(InvalidArgumentException::class);
			$this->expectErrorMessageMatches('/rgb/i');

			new AutoCropFilter((new CMYK())->color("ffffff"), 1);
		}
		
	}