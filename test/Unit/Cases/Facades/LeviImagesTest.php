<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Facades;

	use MehrIt\LeviImages\Facades\LeviImages;
	use MehrIt\LeviImages\LeviImagesManager;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;

	class LeviImagesTest extends TestCase
	{
		public function testAncestorCall() {
			// mock ancestor
			$mock = $this->mockAppSingleton(LeviImagesManager::class, LeviImagesManager::class);
			$mock->expects($this->once())
				->method('raster')
				->with()
				->willReturn(new RasterImageFactory());

			LeviImages::raster();
		}
	}