<?php

	namespace MehrIt\LeviImages\Vector;

	use Contao\ImagineSvg\Image;
	use Contao\ImagineSvg\Imagine;
	use InvalidArgumentException;

	class VectorImageFactory
	{


		/**
		 * Loads a vector image from given file 
		 * @param string $filename The filename
		 * @return Image The image
		 */
		public function open(string $filename): Image {
			
			return $this->imagine()->open($filename);
		}

		/**
		 * Loads a vector image from given buffer
		 * @param string $buffer The buffer
		 * @return Image The image
		 */
		public function load(string $buffer): Image {

			return $this->imagine()->load($buffer);
		}

		/**
		 * Loads a vector image from given resource
		 * @param resource $buffer The resource
		 * @return Image The image
		 */
		public function read($buffer): Image {
			
			if (!is_resource($buffer))
				throw new InvalidArgumentException('Buffer must be a resource.');

			return $this->imagine()->read($buffer);
		}

		/**
		 * Returns an imagine instance
		 * @return Imagine
		 */
		protected function imagine(): Imagine {
			return new Imagine();
		}
	}