<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Enhancements\Inline;

class Test_Enhancement_Inline extends \WP_UnitTestCase {

	function filter__dependency_path( string $path ) : string {
		$search = trailingslashit( ABSPATH ) . 'wp-content/mu-plugins/enhanced-dependencies/';
		$replace = Plugin::directory_path();
		$replace = str_replace( '/dist', '', $replace );

		return str_replace( $search, $replace, $path );
	}

	function test_script() : void {
		$handle = uniqid( 'test-script' );
		$url = trailingslashit( site_url() ) . 'wp-content/mu-plugins/enhanced-dependencies/tests/test-script.js';

		wp_register_script( $handle, $url );

		ob_start();
		wp_print_scripts( $handle );
		$tag = ob_get_clean();

		$expected = '<script id="' . esc_attr( $handle ) . '-inline-js">/* staging conjoined antitoxic defiling strained broadside */</script>';
		$this->assertNotEquals( $tag, $expected );

		add_filter( 'enhanced-dependencies/dependency/path', array( $this, 'filter__dependency_path' ) );
		$actual = Inline::apply( $tag, $handle, true );
		$this->assertEquals( $actual, $expected );
		remove_filter( 'enhanced-dependencies/dependency/path', array( $this, 'filter__dependency_path' ) );
	}

	function test_stylesheet() : void {
		$handle = uniqid( 'test-style' );
		$url = trailingslashit( site_url() ) . 'wp-content/mu-plugins/enhanced-dependencies/tests/test-stylesheet.css';

		wp_register_style( $handle, $url );

		ob_start();
		wp_print_styles( $handle );
		$tag = ob_get_clean();

		$expected = '<style id="' . esc_attr( $handle ) . '-inline-css">/* magnifier slimy symphonic reveler pumice storage */</style>';
		$this->assertNotEquals( $tag, $expected );

		add_filter( 'enhanced-dependencies/dependency/path', array( $this, 'filter__dependency_path' ) );
		$actual = Inline::apply( $tag, $handle, false );
		$this->assertEquals( $actual, $expected );
		remove_filter( 'enhanced-dependencies/dependency/path', array( $this, 'filter__dependency_path' ) );
	}

}

?>