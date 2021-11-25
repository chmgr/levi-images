<?php

	namespace MehrItLeviImagesTest\Unit\Cases\Console;

	
	use MehrItLeviImagesTest\Unit\Cases\TestCase;


	class SelfTestCommandTest extends TestCase
	{



		public function testHandle() {
			
			
			$out = $this->artisan('levi-images:test');
			
			$out->assertExitCode(0);
	
			
		}

	}