<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancements\Inline;

class Test_Enhancement_Inline extends \WP_UnitTestCase {

	function filter__dependency_path( string $path ) : string {
		$search  = trailingslashit( ABSPATH ) . 'wp-content/mu-plugins/enhanced-dependencies/';
		$replace = Plugin::directory_path();
		$replace = str_replace( '/dist', '', $replace );

		return str_replace( $search, $replace, $path );
	}

	function test_notfound() : void {
		$handle = uniqid( 'test-notfound' );
		$url = trailingslashit( site_url() ) . 'notfound.css';

		wp_register_style( $handle, $url, null, '1.0' );

		ob_start();
		wp_print_styles( $handle );
		$tag = ob_get_clean();

		$expected = trim( "<link rel='stylesheet' id='$handle-css' href='http://example.org/notfound.css?ver=1.0' type='text/css' media='all' />" );

		$actual = trim( @Inline::apply( $tag, $handle, false ) );
		$this->assertEquals( $actual, trim( $tag ) );
		$this->assertEquals( $actual, $expected );
	}

	function test_empty() : void {
		$handle = uniqid( 'test-empty' );
		$url = trailingslashit( site_url() ) . 'wp-content/mu-plugins/enhanced-dependencies/tests/test-empty.css';

		wp_register_style( $handle, $url );

		ob_start();
		wp_print_styles( $handle );
		$tag = ob_get_clean();

		$expected = '<style id="' . esc_attr( $handle ) . '-inline-css">/* empty */</style>';
		$this->assertNotEquals( $tag, $expected );

		add_filter( 'enhanced-dependencies/dependency/path', array( $this, 'filter__dependency_path' ) );
		$actual = Inline::apply( $tag, $handle, false );
		$this->assertEquals( $actual, $expected );
		remove_filter( 'enhanced-dependencies/dependency/path', array( $this, 'filter__dependency_path' ) );
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

	function test_default_dir() : void {
		$dependency = Dependency::get( 'jquery-core', true );

		ob_start();
		wp_print_scripts( 'jquery-core' );
		$tag = ob_get_clean();

		$this->assertEquals( "<script type='text/javascript' src='" . site_url() . "/wp-includes/js/jquery/jquery.min.js?ver=" . $dependency->wp_dep()->ver . "' id='jquery-core-js'></script>", trim( $tag ) );

		$builtin = $dependency->wp_dep();
		wp_register_script( 'jquery-core-2', $builtin->src, $builtin->deps, $builtin->ver );
		$dependency = Dependency::get( 'jquery-core-2', true );
		$dependency->set( Inline::KEY );

		ob_start();
		wp_print_scripts( 'jquery-core-2' );
		$tag = ob_get_clean();

		$this->assertNotEquals( "<script type='text/javascript' src='" . site_url() . "/wp-includes/js/jquery/jquery.min.js?ver=" . $dependency->wp_dep()->ver . "' id='jquery-core-2-js'></script>", trim( $tag ) );
	}

}

?>