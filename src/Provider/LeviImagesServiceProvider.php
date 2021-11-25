<?php

	namespace MehrIt\LeviImages\Provider;
	
	use Illuminate\Contracts\Support\DeferrableProvider;
	use Illuminate\Support\ServiceProvider;
	use MehrIt\LeviImages\Commands\SelfTestCommand;
	use MehrIt\LeviImages\LeviImagesManager;
	use MehrIt\LeviImages\Optimization\Optimizer;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use MehrIt\LeviImages\Vector\VectorImageFactory;


	class LeviImagesServiceProvider extends ServiceProvider implements DeferrableProvider
	{

		public function boot() {
			

			if ($this->app->runningInConsole()) {
				
				$this->publishes([
					__DIR__ . '/../../config/levi-images.php' => $this->app->configPath('levi-images.php'),
				], 'config');


				$this->commands([
					SelfTestCommand::class,
				]);

			}
		}


		public function register() {

			$this->mergeConfigFrom(__DIR__ . '/../../config/levi-images.php', 'levi-images');

			$this->app->singleton(RasterImageFactory::class, function($app) {
				return new RasterImageFactory($app['config']['levi-images']['raster'] ?? []);
			});
			
			$this->app->singleton(Optimizer::class, function($app) {
				return new Optimizer($app['config']['levi-images']['optimization'] ?? []);
			});
			
		}

		/**
		 * @inheritDoc
		 */
		public function provides() {
			return [
				RasterImageFactory::class,
				VectorImageFactory::class,
				LeviImagesManager::class,
				Optimizer::class,
			];
		}


	}