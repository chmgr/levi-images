<?php

	namespace MehrIt\LeviImages\Commands;

	use Illuminate\Console\Command;
	use MehrIt\LeviImages\Optimization\Optimizer;
	use MehrIt\LeviImages\Raster\RasterImageFactory;
	use Spatie\ImageOptimizer\Image;

	class SelfTestCommand extends Command
	{
		protected $signature = 'levi-images:test';

		protected $description = 'Run image system self test';

		/**
		 * @var RasterImageFactory
		 */
		protected $raster;

		/**
		 * @var Optimizer
		 */
		protected $optimizer;

		/**
		 * @param RasterImageFactory $raster
		 * @param Optimizer $optimizer
		 */
		public function __construct(RasterImageFactory $raster, Optimizer $optimizer) {
			parent::__construct();
			
			$this->raster = $raster;
			$this->optimizer = $optimizer;
		}


		public function handle() {
			
			// check available drivers
			$this->line('Checking installed raster image drivers:');
			$anySupported = false;
			$vipsSupported = false;
			foreach($this->raster->drivers() as $currDriver) {
				
				
				if ($this->raster->isDriverSupported($currDriver)) {
					$this->line("  [ <info>OK</info> ]  Driver \"{$currDriver}\"");
					$anySupported = true;
					if ($currDriver === 'vips')
						$vipsSupported = true;
				}
				else {
					$this->line("  [    ]  Driver \"{$currDriver}\" ");
				}
			}
			
			
			if ($vipsSupported) {


				/** @noinspection PhpUndefinedFunctionInspection */
				$vipsVersion = vips_version();
				
				if (version_compare($vipsVersion, '8.6', '<=')) {
					$this->line('');
					$this->warn("libvips {$vipsVersion}: 'Paste()' and 'rotating by angles other than multipliers of 90' is only supported by libvips >= 8.6");
				}
			}
			
			if (!$anySupported) {
				$this->error("No supported raster image driver is available");
				return 1;
			}
			
			
			// get the chosen driver
			$driver = $this->raster->driver();
			$this->line('');
			$this->line('Used raster image driver: ');
			$this->info('  ' . $driver);
			if ($driver == 'vips') {
				$this->line('');
				$this->warn("The vips driver is a good choice in terms of performance and memory usage.");
				$this->warn("However it does not support all features. See: ");
				$this->warn('  https://github.com/rokka-io/imagine-vips#missing-stuff');
			}
			
			
			$this->line('');
			$this->line('Checking installed optimizers:');
			
			$availableOptimizers = [];
			foreach($this->optimizer->getOptimizers() as $curr) {
				$binaryName = $curr->binaryName();
				
				if (trim(shell_exec("which \"{$binaryName}\"")) != '') {
					$this->line("  [ <info>OK</info> ]  Optimizer \"{$binaryName}\" ");

					$availableOptimizers[] = $curr;
				}
				else {
					$this->line("  [    ]  Optimizer \"{$binaryName}\" ");
				}
				
			}


			$this->line('');
			$this->line('Checking optimizable image formats:');
			$path = __DIR__ . '/../../resources/images/optimizer-test';
			$files = array_diff(scandir($path), array('.', '..'));
			$optimizableTypes = [];
			foreach($files as $currFile) {
				
				$testImage = new Image("{$path}/{$currFile}");
				
				foreach ($availableOptimizers as $curr) {
					
					if ($curr->canHandle($testImage)) 
						$optimizableTypes[$testImage->mime()][] = $curr->binaryName();
					
				}

				if (empty($optimizableTypes[$testImage->mime()]))
					$optimizableTypes[$testImage->mime()] = false;
				
			}
			
			foreach($optimizableTypes as $currType => $binaries) {
				if ($binaries)
					$this->line("  [  <info>OK</info>  ]  $currType  (" . implode(', ', $binaries) . ')');
				else
					$this->line("  [ <comment>miss</comment> ]  $currType ");
			}
			
			
			
			return 0;			
		}
	}