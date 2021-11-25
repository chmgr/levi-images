<?php

	namespace MehrIt\LeviImages\Util;

	use RuntimeException;

	trait TemporaryFiles
	{
		/**
		 * Creates a temporary file
		 * @param string|null $prefix The filename prefix
		 * @return string The file name
		 */
		protected function createTempFile(string $prefix = null, string $ext = null) {
			
			$filename = tempnam(config('levi-images.temp_dir', sys_get_temp_dir()), config('levi-images.temp_prefix') . $prefix);
			if (!$filename)
				throw new RuntimeException("Failed to create temporary file");
			
			if ($ext) {
				
				$newFilename = "{$filename}.{$ext}";
				
				if (!rename($filename, $newFilename))
					throw new RuntimeException("Failed to rename temporary file \"{$filename}\" to \"{$newFilename}\"");
				
				return $newFilename;
			}
			

			return $filename;
		}

		/**
		 * Executes the given callback with a temporary file created
		 * @param callable $callback The callback. Will receive filename as argument
		 * @param string|null $prefix The filename prefix
		 * @return mixed The callback return
		 */
		function withTempFile(callable $callback, string $prefix = null, string $ext = null) {

			try {
				$tempFile = $this->createTempFile($prefix, $ext);

				return call_user_func($callback, $tempFile);
			}
			finally {
				if (!empty($tempFile) && file_exists($tempFile))
					unlink($tempFile);
			}

		}
	}