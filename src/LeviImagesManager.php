<?php

	namespace MehrIt\LeviImages;

	use MehrIt\LeviImages\Optimization\Optimizer;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrIt\LeviImages\Vector\VectorImageFactory;

	class LeviImagesManager
	{

		/**
		 * @var RasterImageFactory
		 */
		protected $raster;

		/**
		 * @var VectorImageFactory
		 */
		protected $vector;

		/**
		 * @var Optimizer
		 */
		protected $optimizer;

		/**
		 * Returns a raster image factory
		 * @return RasterImageFactory
		 */
		public function raster(): RasterImageFactory {
			if (!$this->raster)
				$this->raster = app(RasterImageFactory::class);
			
			return $this->raster;
		}

		/**
		 * Returns a vector image factory
		 * @return VectorImageFactory
		 */
		public function vector(): VectorImageFactory {
			if (!$this->vector)
				$this->vector = app(VectorImageFactory::class);

			return $this->vector;
		}

		/**
		 * Returns an optimizer
		 * @return Optimizer
		 */
		public function optimizer(): Optimizer {
			if (!$this->optimizer)
				$this->optimizer = app(Optimizer::class);

			return $this->optimizer;	
		}
		
	}