<?php

namespace Enhanced_Dependencies\Tests;

class Test_Functions extends \WP_UnitTestCase {

	function test_functions_defined() : void {
		foreach ( array(
			'wp_enhance_script',
			'wp_dehance_script',
			'wp_enhance_style',
			'wp_dehance_style',
		) as $function_name )
			$this->assertTrue( function_exists( $function_name ) );
	}

}