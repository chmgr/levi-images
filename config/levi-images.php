<?php
	return [
		'temp_dir' => env('TEMP_DIR'),

		'temp_prefix' => env('TEMP_PFX', 'laravel'),

		'raster' => [

			/*
			 * Configures the raster image driver to use. Available options are "vips", "imagick", "gmagick" and "gd". If
			 * "auto" is set, the first available driver is chosen.
			 */
			'driver' => 'auto',

			/*
			 * Special options for the vips driver.
			 */
			'vips' => [
				//'max_mem'     => '', // vips_cache_set_max_mem($max_mem)
				//'max_ops'     => '', // vips_cache_set_max($max_ops)
				//'max_files'   => '', // vips_cache_set_max_files($max_files)
				//'concurrency' => '', // vips_concurrency_set($concurrency)
			]
		],

		'optimization' => [

			/*
			 * Timeout for each individual optimizer to run (in seconds) 
			 */
			'timeout' => 60, 
		]
	];