<?php

	namespace MehrItLeviImagesTest\Unit\Cases;

	use MehrIt\LeviImages\LeviImagesManager;
	use MehrIt\LeviImages\Optimization\Optimizer;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrIt\LeviImages\Vector\VectorImageFactory;

	class LeviImagesManagerTest extends TestCase
	{

		public function testRaster() {
			
			$manager = new LeviImagesManager();
			
			$this->assertInstanceOf(RasterImageFactory::class, $manager->raster());
			
		}

		public function testVector() {

			$manager = new LeviImagesManager();

			$this->assertInstanceOf(VectorImageFactory::class, $manager->vector());

		}
		
		public function testOptimizer() {

			$manager = new LeviImagesManager();

			$this->assertInstanceOf(Optimizer::class, $manager->optimizer());

		}
	}