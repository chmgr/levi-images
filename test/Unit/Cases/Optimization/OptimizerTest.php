<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Optimization;

	use Imagick;
	use Imagine\Imagick\Imagine;
	use InvalidArgumentException;
	use MehrIt\LeviImages\Optimization\Optimizer;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;
	use Spatie\ImageOptimizer\Optimizers\Cwebp;
	use Spatie\ImageOptimizer\Optimizers\Gifsicle;
	use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
	use Spatie\ImageOptimizer\Optimizers\Optipng;
	use Spatie\ImageOptimizer\Optimizers\Pngquant;
	use Spatie\ImageOptimizer\Optimizers\Svgo;

	class OptimizerTest extends TestCase
	{
		/**
		 * Returns if the given optimizer is installed
		 * @param \Spatie\ImageOptimizer\Optimizer $optimizer
		 * @return bool
		 */
		protected function isOptimizerInstalled(\Spatie\ImageOptimizer\Optimizer $optimizer): bool {
			$binaryName = $optimizer->binaryName();

			return trim(shell_exec("which \"{$binaryName}\"")) != '';
		}

		public function testTimeoutIsReadFromConfig() {

			$o = new Optimizer([
				'timeout' => 34,
			]);

			$this->assertSame(34, $o->getTimeout());

		}

		public function testOptimizersResolverIsUsed_returningSingleOptimizer() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();


				$opt1 = \Mockery::mock(\Spatie\ImageOptimizer\Optimizer::class);
				$opt1
					->shouldReceive('getCommand')
					->once()
					->andReturn('testOptimize -run');
				$opt1
					->shouldReceive('binaryName')
					->andReturn('testOptimize');
				$opt1
					->shouldReceive('canHandle')
					->andReturn(true);
				$opt1
					->shouldReceive('setImagePath')
					->andReturnSelf();
				$opt1
					->shouldReceive('setOptions')
					->andReturnSelf();


				$o->setOptimizersResolver(function () use ($opt1) {
					return $opt1;
				});


				$this->assertSame($o, $o->optimizeFile($file));

			});

		}

		public function testOptimizersResolverIsUsed_returningMultipleOptimizers() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();


				$opt1 = \Mockery::mock(\Spatie\ImageOptimizer\Optimizer::class);
				$opt1
					->shouldReceive('getCommand')
					->once()
					->andReturn('testOptimize -run');
				$opt1
					->shouldReceive('binaryName')
					->andReturn('testOptimize');
				$opt1
					->shouldReceive('canHandle')
					->andReturn(true);
				$opt1
					->shouldReceive('setImagePath')
					->andReturnSelf();
				$opt1
					->shouldReceive('setOptions')
					->andReturnSelf();

				$opt2 = \Mockery::mock(\Spatie\ImageOptimizer\Optimizer::class);
				$opt2
					->shouldReceive('getCommand')
					->once()
					->andReturn('testOptimize -run');
				$opt2
					->shouldReceive('binaryName')
					->andReturn('testOptimize');
				$opt2
					->shouldReceive('canHandle')
					->andReturn(true);
				$opt2
					->shouldReceive('setImagePath')
					->andReturnSelf();
				$opt2
					->shouldReceive('setOptions')
					->andReturnSelf();


				$o->setOptimizersResolver(function () use ($opt1, $opt2) {
					return [$opt1, $opt2];
				});


				$this->assertSame($o, $o->optimizeFile($file));

			});

		}

		public function testDefaultChain() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();

				$targetFile = "{$file}1";

				try {
					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(90, 140)->getColor()
					);
					$this->assertColorMatching(
						['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
						$im->getImagePixelColor(240, 140)->getColor()
					);
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});

		}

		public function testUseOptimizers() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();


				$opt1 = \Mockery::mock(\Spatie\ImageOptimizer\Optimizer::class);
				$opt1
					->shouldReceive('getCommand')
					->once()
					->andReturn('testOptimize -run');
				$opt1
					->shouldReceive('binaryName')
					->andReturn('testOptimize');
				$opt1
					->shouldReceive('canHandle')
					->andReturn(true);
				$opt1
					->shouldReceive('setImagePath')
					->andReturnSelf();
				$opt1
					->shouldReceive('setOptions')
					->andReturnSelf();

				$opt2 = \Mockery::mock(\Spatie\ImageOptimizer\Optimizer::class);
				$opt2
					->shouldReceive('getCommand')
					->once()
					->andReturn('testOptimize -run');
				$opt2
					->shouldReceive('binaryName')
					->andReturn('testOptimize');
				$opt2
					->shouldReceive('canHandle')
					->andReturn(true);
				$opt2
					->shouldReceive('setImagePath')
					->andReturnSelf();
				$opt2
					->shouldReceive('setOptions')
					->andReturnSelf();
				
				$opt3 = \Mockery::mock(\Spatie\ImageOptimizer\Optimizer::class);
				$opt3
					->shouldReceive('getCommand')
					->once()
					->andReturn('testOptimize -run');
				$opt3
					->shouldReceive('binaryName')
					->andReturn('testOptimize');
				$opt3
					->shouldReceive('canHandle')
					->andReturn(true);
				$opt3
					->shouldReceive('setImagePath')
					->andReturnSelf();
				$opt3
					->shouldReceive('setOptions')
					->andReturnSelf();


				$o1 = $o->useOptimizers([$opt1, $opt2]);
				$o2 = $o1->useOptimizers([$opt3]);


				$this->assertSame($o1, $o1->optimizeFile($file));
				$this->assertSame($o2, $o2->optimizeFile($file));

			});

		}

		public function testOptimizeFile_png() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Pngquant()))
						return new Pngquant([
							'--quality=85',
							'--force',
							'--skip-if-larger',
						]);
					if ($this->isOptimizerInstalled(new Optipng()))
						return new Optipng([
							'-i0',
							'-o2',
							'-quiet',
						]);

					$this->markTestSkipped('No optimizer for PNG installed.');
				});

				$this->assertSame($o, $o->optimizeFile($file));

				$im = new Imagick($file);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, filesize($file), 'Optimized file size is not smaller.');
				$this->assertNotSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeFile_jpg() {

			$this->withTempTestImage('test.jpg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Jpegoptim()))
						return (new Jpegoptim([
							'--max=85',
							'--strip-all',
							'--all-progressive',
						]));

					$this->markTestSkipped('No optimizer for JPG installed.');
				});

				$this->assertSame($o, $o->optimizeFile($file));

				$im = new Imagick($file);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, filesize($file), 'Optimized file size is not smaller.');
				$this->assertNotSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeFile_webp() {

			$this->withTempTestImage('test.webp', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Cwebp()))
						return new Cwebp([
							'-m 6',
							'-pass 10',
							'-mt',
							'-q 80',
						]);

					$this->markTestSkipped('No optimizer for WebP installed.');
				});

				$this->assertSame($o, $o->optimizeFile($file));

				$im = new Imagick($file);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, filesize($file), 'Optimized file size is not smaller.');
				$this->assertNotSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeFile_svg() {


			$this->withTempTestImage('test.svg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Svgo()))
						return new Svgo([
							'--disable={cleanupIDs,removeViewBox}',
						]);

					$this->markTestSkipped('No optimizer for SVG installed.');
				});


				$this->assertSame($o, $o->optimizeFile($file));

				$im = new Imagick($file);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, filesize($file), 'Optimized file size is not smaller.');
				$this->assertNotSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeFile_svg_otherExtension() {


			$this->withTempTestImage('test.svg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Svgo()))
						return new Svgo([
							'--disable={cleanupIDs,removeViewBox}',
						]);

					$this->markTestSkipped('No optimizer for SVG installed.');
				});


				$this->assertSame($o, $o->optimizeFile($file));


				$im = new Imagick();
				$im->setFormat('svg');
				$im->readImage($file);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, filesize($file), 'Optimized file size is not smaller.');
				$this->assertNotSame($origContent, file_get_contents($file));

			}, 'txt');
		}

		public function testOptimizeFile_gif() {

			$this->withTempTestImage('test_photo.gif', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Gifsicle()))
						return new Gifsicle([
							'-b',
							'-O3',
							'--colors=64'
						]);

					$this->markTestSkipped('No optimizer for GIF installed.');
				});

				$this->assertSame($o, $o->optimizeFile($file));

				$im = new Imagick($file);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(590, 190)->getColor()
				);

				$this->assertLessThan($origSize, filesize($file), 'Optimized file size is not smaller.');
				$this->assertNotSame($origContent, file_get_contents($file));

			});
		}


		public function testOptimizeFile_withOutputPath_png() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);

				$targetFile = "{$file}1";

				try {

					// setup single optimizer
					$o->setOptimizersResolver(function () {
						if ($this->isOptimizerInstalled(new Pngquant()))
							return new Pngquant([
								'--quality=85',
								'--force',
								'--skip-if-larger',
							]);
						if ($this->isOptimizerInstalled(new Optipng()))
							return new Optipng([
								'-i0',
								'-o2',
								'-quiet',
							]);

						$this->markTestSkipped('No optimizer for PNG installed.');
					});

					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(90, 140)->getColor()
					);
					$this->assertColorMatching(
						['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
						$im->getImagePixelColor(240, 140)->getColor()
					);

					$this->assertLessThan($origSize, filesize($targetFile), 'Optimized file size is not smaller.');
					$this->assertSame($origContent, file_get_contents($file));
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});
		}

		public function testOptimizeFile_withOutputPath_jpg() {

			$this->withTempTestImage('test.jpg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);

				$targetFile = "{$file}1";

				try {

					// setup single optimizer
					$o->setOptimizersResolver(function () {
						if ($this->isOptimizerInstalled(new Jpegoptim()))
							return (new Jpegoptim([
								'--max=85',
								'--strip-all',
								'--all-progressive',
							]));

						$this->markTestSkipped('No optimizer for JPG installed.');
					});

					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(90, 140)->getColor()
					);
					$this->assertColorMatching(
						['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
						$im->getImagePixelColor(240, 140)->getColor()
					);

					$this->assertLessThan($origSize, filesize($targetFile), 'Optimized file size is not smaller.');
					$this->assertSame($origContent, file_get_contents($file));
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});
		}

		public function testOptimizeFile_withOutputPath_webp() {

			$this->withTempTestImage('test.webp', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);

				$targetFile = "{$file}1";

				try {

					// setup single optimizer
					$o->setOptimizersResolver(function () {
						if ($this->isOptimizerInstalled(new Cwebp()))
							return new Cwebp([
								'-m 6',
								'-pass 10',
								'-mt',
								'-q 80',
							]);

						$this->markTestSkipped('No optimizer for WebP installed.');
					});;

					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(90, 140)->getColor()
					);
					$this->assertColorMatching(
						['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
						$im->getImagePixelColor(240, 140)->getColor()
					);

					$this->assertLessThan($origSize, filesize($targetFile), 'Optimized file size is not smaller.');
					$this->assertSame($origContent, file_get_contents($file));
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});
		}


		public function testOptimizeFile_withOutputPath_svg() {

			$this->withTempTestImage('test.svg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);

				$targetFile = str_replace('.', '1.', $file);

				try {

					// setup single optimizer
					$o->setOptimizersResolver(function () {
						if ($this->isOptimizerInstalled(new Svgo()))
							return new Svgo([
								'--disable={cleanupIDs,removeViewBox}',
							]);

						$this->markTestSkipped('No optimizer for SVG installed.');
					});

					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick();
					$im->setFormat('svg');
					$im->readImage($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(90, 140)->getColor()
					);
					$this->assertColorMatching(
						['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
						$im->getImagePixelColor(240, 140)->getColor()
					);

					$this->assertLessThan($origSize, filesize($targetFile), 'Optimized file size is not smaller.');
					$this->assertSame($origContent, file_get_contents($file));
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});
		}

		public function testOptimizeFile_withOutputPath_svg_otherExtension() {

			$this->withTempTestImage('test.svg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);

				$targetFile = "{$file}.xml";

				try {

					// setup single optimizer
					$o->setOptimizersResolver(function () {
						if ($this->isOptimizerInstalled(new Svgo()))
							return new Svgo([
								'--disable={cleanupIDs,removeViewBox}',
							]);

						$this->markTestSkipped('No optimizer for SVG installed.');
					});

					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick();
					$im->setFormat('svg');
					$im->readImage($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(90, 140)->getColor()
					);
					$this->assertColorMatching(
						['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
						$im->getImagePixelColor(240, 140)->getColor()
					);

					$this->assertLessThan($origSize, filesize($targetFile), 'Optimized file size is not smaller.');
					$this->assertSame($origContent, file_get_contents($file));
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});
		}

		public function testOptimizeFile_withOutputPath_gif() {

			$this->withTempTestImage('test_photo.gif', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);

				$targetFile = "{$file}1";

				try {

					// setup single optimizer
					$o->setOptimizersResolver(function () {
						if ($this->isOptimizerInstalled(new Gifsicle()))
							return new Gifsicle([
								'-b',
								'-O3',
								'--colors=64'
							]);

						$this->markTestSkipped('No optimizer for GIF installed.');
					});

					$this->assertSame($o, $o->optimizeFile($file, $targetFile));

					$im = new Imagick($targetFile);

					$this->assertColorMatching(
						['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
						$im->getImagePixelColor(590, 190)->getColor()
					);

					$this->assertLessThan($origSize, filesize($targetFile), 'Optimized file size is not smaller.');
					$this->assertSame($origContent, file_get_contents($file));
				}
				finally {
					if (file_exists($targetFile))
						unlink($targetFile);
				}
			});
		}

		public function testOptimizeResource_png() {

			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Pngquant()))
						return new Pngquant([
							'--quality=85',
							'--force',
							'--skip-if-larger',
						]);
					if ($this->isOptimizerInstalled(new Optipng()))
						return new Optipng([
							'-i0',
							'-o2',
							'-quiet',
						]);

					$this->markTestSkipped('No optimizer for PNG installed.');
				});

				$res = $o->optimizeResource(fopen($file, 'r'));

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeResource_jpg() {

			$this->withTempTestImage('test.jpg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Jpegoptim()))
						return (new Jpegoptim([
							'--max=85',
							'--strip-all',
							'--all-progressive',
						]));

					$this->markTestSkipped('No optimizer for JPG installed.');
				});

				$res = $o->optimizeResource(fopen($file, 'r'));

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeResource_webp() {

			$this->withTempTestImage('test.webp', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Cwebp()))
						return new Cwebp([
							'-m 6',
							'-pass 10',
							'-mt',
							'-q 80',
						]);

					$this->markTestSkipped('No optimizer for WebP installed.');
				});

				$res = $o->optimizeResource(fopen($file, 'r'));

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeResource_svg() {

			$this->withTempTestImage('test.svg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Svgo()))
						return new Svgo([
							'--disable={cleanupIDs,removeViewBox}',
						]);

					$this->markTestSkipped('No optimizer for SVG installed.');
				});

				$res = $o->optimizeResource(fopen($file, 'r'));

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->setFormat('svg');
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeResource_gif() {

			$this->withTempTestImage('test_photo.gif', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Gifsicle()))
						return new Gifsicle([
							'-b',
							'-O3',
							'--colors=64'
						]);

					$this->markTestSkipped('No optimizer for GIF installed.');
				});

				$res = $o->optimizeResource(fopen($file, 'r'));

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(590, 190)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeImage_imagick_png() {

			if (!extension_loaded('imagick'))
				$this->markTestSkipped('Imagick extension is required for this test');


			$this->withTempTestImage('test.png', function ($file) {

				$o = new Optimizer();

				$origSize = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Pngquant()))
						return new Pngquant([
							'--quality=85',
							'--force',
							'--skip-if-larger',
						]);
					if ($this->isOptimizerInstalled(new Optipng()))
						return new Optipng([
							'-i0',
							'-o2',
							'-quiet',
						]);

					$this->markTestSkipped('No optimizer for PNG installed.');
				});


				$res = $o->optimizeImage((new Imagine())->open($file), Optimizer::FORMAT_PNG);

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeImage_imagick_jpg() {

			if (!extension_loaded('imagick'))
				$this->markTestSkipped('Imagick extension is required for this test');


			$this->withTempTestImage('test.jpg', function ($file) {

				$o = new Optimizer();

				$origSize = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Jpegoptim()))
						return (new Jpegoptim([
							'--max=85',
							'--strip-all',
							'--all-progressive',
						]));

					$this->markTestSkipped('No optimizer for JPG installed.');
				});


				$res = $o->optimizeImage((new Imagine())->open($file), Optimizer::FORMAT_JPEG);

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeImage_imagick_webp() {

			if (!extension_loaded('imagick'))
				$this->markTestSkipped('Imagick extension is required for this test');


			$this->withTempTestImage('test.webp', function ($file) {

				$o = new Optimizer();

				$origSize = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Cwebp()))
						return new Cwebp([
							'-m 6',
							'-pass 10',
							'-mt',
							'-q 80',
						]);

					$this->markTestSkipped('No optimizer for WebP installed.');
				});


				$res = $o->optimizeImage((new Imagine())->open($file), Optimizer::FORMAT_WEBP);

				$compressedSize = strlen(stream_get_contents($res));
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}

		public function testOptimizeImage_imagick_gif() {

			if (!extension_loaded('imagick'))
				$this->markTestSkipped('Imagick extension is required for this test');


			$this->withTempTestImage('test_photo.gif', function ($file) {

				$o = new Optimizer();

				$origSize = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Gifsicle()))
						return new Gifsicle([
							'-b',
							'-O3',
							'--colors=64'
						]);

					$this->markTestSkipped('No optimizer for GIF installed.');
				});


				$res = $o->optimizeImage((new Imagine())->open($file), Optimizer::FORMAT_GIF);

				$compressedSize = strlen(stream_get_contents($res));
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(590, 190)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));

			});
		}
		
		public function testOptimizeImage_imagick_invalidFormat() {

			if (!extension_loaded('imagick'))
				$this->markTestSkipped('Imagick extension is required for this test');


			$this->withTempTestImage('test_photo.gif', function ($file) {

				$o = new Optimizer();

				$this->expectException(InvalidArgumentException::class);

				$o->optimizeImage((new Imagine())->open($file), 'doc');
			});
		}

		public function testOptimizeImage_vector_svg() {

			$this->withTempTestImage('test.svg', function ($file) {

				$o = new Optimizer();

				$origSize    = filesize($file);
				$origContent = file_get_contents($file);


				// setup single optimizer
				$o->setOptimizersResolver(function () {
					if ($this->isOptimizerInstalled(new Svgo()))
						return new Svgo([
							'--disable={cleanupIDs,removeViewBox}',
						]);

					$this->markTestSkipped('No optimizer for SVG installed.');
				});


				$res = $o->optimizeImage((new \Contao\ImagineSvg\Imagine())->open($file), Optimizer::FORMAT_SVG);

				$compressedContent = stream_get_contents($res);
				$compressedSize    = strlen($compressedContent);
				$this->assertGreaterThan(0, $compressedSize);
				rewind($res);


				$im = new Imagick();
				$im->setFormat('svg');
				$im->readImageFile($res);

				$this->assertColorMatching(
					['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1],
					$im->getImagePixelColor(90, 140)->getColor()
				);
				$this->assertColorMatching(
					['r' => 0, 'g' => 255, 'b' => 0, 'a' => 1],
					$im->getImagePixelColor(240, 140)->getColor()
				);

				$this->assertLessThan($origSize, $compressedSize, 'Optimized file size is not smaller.');
				$this->assertSame($origContent, file_get_contents($file));
				$this->assertStringContainsString('<svg', $compressedContent);

			});
		}
		
		
	}