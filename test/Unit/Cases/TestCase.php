<?php

	namespace MehrItLeviImagesTest\Unit\Cases;

	use Illuminate\Support\Arr;
	use MehrIt\LeviImages\Facades\LeviImages;
	use MehrIt\LeviImages\Provider\LeviImagesServiceProvider;
	use MehrIt\LeviImages\Util\TemporaryFiles;

	class TestCase extends \Orchestra\Testbench\TestCase
	{
		use TemporaryFiles;
		
		protected function getPackageProviders($app) {
			
			return [
				LeviImagesServiceProvider::class,
			];

		}

		/**
		 * @inheritDoc
		 */
		protected function getPackageAliases($app) {

			return [
				'LeviImages' => LeviImages::class,
			];
		}
		
		protected function testOutputPath(string $ext) {
			
			$cls = Arr::last(explode('\\', get_class($this)));
			$function = $this->getName();
			return __DIR__ . "/../../out/{$cls}_{$function}.{$ext}"; 
			
		}


		/**
		 * @param $abstract
		 * @param null $class
		 * @return \PHPUnit\Framework\MockObject\MockObject
		 */
		protected function mockAppSingleton($abstract, $class = null) {

			if ($class === null)
				$class = $abstract;

			$mock = $this->getMockBuilder($class)->disableOriginalConstructor()->getMock();

			app()->singleton($abstract, function () use ($mock) {
				return $mock;
			});

			return $mock;
		}
		
		protected function withTempTestImage(string $testImageFile, callable $callback, string $tempExtension = null) {
			
			return $this->withTempFile(function ($file) use($testImageFile, $callback) {
			
				file_put_contents($file, fopen(__DIR__ . "/../../resources/{$testImageFile}", 'r'));
				
				return call_user_func($callback, $file);				
			}, null, $tempExtension ?: pathinfo($testImageFile, PATHINFO_EXTENSION));
			
		}

		protected function assertColorMatching(array $expected, array $actual, int $tolerance = 10) {
			
			foreach($actual as $comp => $value) {
				
				// convert non-alpha channel (null) to fully opaque (100)
				if ($value === null)
					$value = 100;
				
				$this->assertInTolerance($expected[$comp], $value, $tolerance);
			}
			
		}
		
		protected function assertInTolerance($expected, $actual, $tolerance = 10) {
			$this->assertGreaterThanOrEqual($expected - $tolerance / 2, $actual);
			$this->assertLessThanOrEqual($expected + $tolerance / 2, $actual);
		}
		
	}