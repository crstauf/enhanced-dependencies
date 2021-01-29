<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Dependency;

class Test_Enhancement_Defer extends \WP_UnitTestCase {

	function test_script() : void {
		$handle = uniqid( 'test-enhancement-script' );
		$src = site_url( 'test-enhancement-script.js' );
		wp_register_script( $handle, $src, array(), '0.1' );

		$dependency = Dependency::get( $handle, true );
		$dependency->set( 'defer' );

		ob_start();
		\wp_print_scripts( $handle );
		$actual = trim( ob_get_clean() );

		$this->assertEquals( "<script defer type='text/javascript' src='$src?ver=0.1' id='$handle-js'></script>", $actual );
	}

	function test_style() : void {
		$handle = uniqid( 'test-enhancement-style' );
		$src = site_url( 'test-enhancement-style.css' );
		wp_register_style( $handle, $src, array(), '0.1' );

		$dependency = Dependency::get( $handle, true );
		$dependency->set( 'defer' );

		ob_start();
		\wp_print_styles( $handle );
		$actual = trim( ob_get_clean() );

		$this->assertEquals( "<link rel='stylesheet' id='$handle-css'  href='$src?ver=0.1' type='text/css' media='all' />", $actual );
	}

}

?>