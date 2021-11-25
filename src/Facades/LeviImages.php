<?php

	namespace MehrIt\LeviImages\Facades;

	use Illuminate\Support\Facades\Facade;
	use MehrIt\LeviImages\LeviImagesManager;

	class LeviImages extends Facade
	{

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 */
		protected static function getFacadeAccessor() {
			return LeviImagesManager::class;
		}

	}