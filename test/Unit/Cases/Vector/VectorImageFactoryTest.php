<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Vector;

	use Contao\ImagineSvg\Image;
	use MehrIt\LeviImages\Vector\VectorImageFactory;
	use MehrItLeviImagesTest\Unit\Cases\TestCase;

	class VectorImageFactoryTest extends TestCase
	{

		public function testOpen() {
			
			$this->withTempTestImage('test.svg', function ($file) {
				
				$f = new VectorImageFactory();
				
				$im = $f->open($file);
				
				$this->assertInstanceOf(Image::class, $im);
				$this->assertSame(542, $im->getSize()->getWidth());
				
			});
			
		}
		
		public function testLoad() {
			
			$this->withTempTestImage('test.svg', function ($file) {
				
				$f = new VectorImageFactory();
				
				$im = $f->load(file_get_contents($file));
				
				$this->assertInstanceOf(Image::class, $im);
				$this->assertSame(542, $im->getSize()->getWidth());
				
			});
			
		}

		public function testRead() {

			$this->withTempTestImage('test.svg', function ($file) {

				$f = new VectorImageFactory();

				$im = $f->read(fopen($file, 'r'));

				$this->assertInstanceOf(Image::class, $im);
				$this->assertSame(542, $im->getSize()->getWidth());

			});

		}

	}