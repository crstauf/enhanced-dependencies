<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Dependency;

class Test_Enhancement_Async extends \WP_UnitTestCase {

	function test_script() : void {
		$dependency = Dependency::get( 'jquery-core', true );
		$dependency->set( 'async' );

		ob_start();
		\wp_print_scripts( 'jquery-core' );
		$actual = trim( ob_get_clean() );

		$this->assertEquals( "<script async type='text/javascript' src='http://example.org/wp-includes/js/jquery/jquery.min.js?ver=3.5.1' id='jquery-core-js'></script>", $actual );
	}

	function test_stylesheet() : void {
		$handle = uniqid( 'test-enhancement-dependency' );
		wp_register_style( $handle, site_url( 'test-enhancement-dependency.css' ), array(), '0.1' );

		$dependency = Dependency::get( $handle, false );
		$dependency->set( 'async' );

		ob_start();
		\wp_print_styles( $handle );
		$actual = trim( ob_get_clean() );

		$expected = "<link rel='stylesheet' id='$handle-css'  href='http://example.org/test-enhancement-dependency.css?ver=0.1' type='text/css' media='print' onload='this.media=\"all\"' />
<noscript><link rel='stylesheet' id='$handle-css'  href='http://example.org/test-enhancement-dependency.css?ver=0.1' type='text/css' media='all' /></noscript>";

		$this->assertEquals( $expected, $actual );
	}

}