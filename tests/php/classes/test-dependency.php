<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Dependency;

class Test_Dependency extends \WP_UnitTestCase {

	function test_constant() : void {
		$this->assertSame( '_enhancements', Dependency::EXTRA_DATA_KEY );
	}

	function test_properties() : void {
		$this->assertSame( array(), get_class_vars( Dependency::class ) );
	}

	function test_get__unregistered() : void {
		$this->assertInstanceOf( Dependency::class, Dependency::get( rand(), true ) );
	}

	function test_get__new() : void {
		$test_handle = uniqid( 'test-enhancement-script' );
		$builtin = wp_scripts()->registered['jquery-core'];

		wp_register_script( $test_handle, $builtin->src, $builtin->deps, $builtin->ver );

		$this->assertEmpty( wp_scripts()->get_data( $test_handle, Dependency::EXTRA_DATA_KEY ) );

		$test_enhancement_key = 'test-enhancement';
		wp_enhance_script( $test_handle, $test_enhancement_key );
		$this->assertInstanceOf( Dependency::class, Dependency::get( $test_handle, true ) );
	}

	function test_helper() : void {
		$dependency = Dependency::get( 'jquery-core', true );
		$this->assertInstanceOf( 'WP_Scripts', $dependency->helper() );

		$dependency = Dependency::get( 'admin-bar', false );
		$this->assertInstanceOf( 'WP_Styles', $dependency->helper() );
	}

	function test_set__empty() : void {
		$dependency = new Dependency;
		$this->assertInstanceOf( Dependency::class, $dependency->set( 'test-enhancement-key' ) );
	}

	function test_set__exists() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_handle = uniqid( 'test-enhancement-dependency' );

		$dependency = new Dependency( $test_enhancement_handle );
		$this->assertInstanceOf( Dependency::class, $dependency->set( $test_enhancement_key, range( 1, 5 ) ) );
		$this->assertEquals( range( 1, 5 ), $dependency->enhancements[ $test_enhancement_key ] );

		$this->assertTrue( !!did_action( 'set_dependency_enhancement' ) );
	}

	function test_remove__empty() : void {
		$dependency = new Dependency;
		$this->assertInstanceOf( Dependency::class, $dependency->remove( 'test-enhancement-key' ) );
	}

	function test_remove__exists() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_handle = uniqid( 'test-enhancement-dependency' );

		$dependency = new Dependency( $test_enhancement_handle );

		$dependency->set( $test_enhancement_key, range( 1, 5 ) );
		$dependency->set( $test_enhancement_key . '-2', range( 5, 10 ) );
		$this->assertEquals( range( 1,  5 ), $dependency->enhancements[ $test_enhancement_key ] );
		$this->assertEquals( range( 5, 10 ), $dependency->enhancements[ $test_enhancement_key . '-2' ] );
		$this->assertInstanceOf( Dependency::class, $dependency->remove() );
		$this->assertEmpty( $dependency->enhancements );

		$dependency->set( $test_enhancement_key, range( 5, 10 ) );
		$this->assertInstanceOf( Dependency::class, $dependency->remove( $test_enhancement_key ) );
		$this->assertArrayNotHasKey( $test_enhancement_key, $dependency->enhancements );
	}

	function test_has() : void {
		$dependency = new Dependency( uniqid( 'test-enhancement-dependency' ) );
		$this->assertFalse( $dependency->has() );
		$this->assertFalse( $dependency->has( 'test-enhancement-key' ) );
		$this->assertFalse( $dependency->has( 'test-enhancement-key-notexist' ) );

		$dependency->set( 'test-enhancement-key' );
		$this->assertTrue( $dependency->has() );
		$this->assertTrue( $dependency->has( 'test-enhancement-key' ) );
		$this->assertFalse( $dependency->has( 'test-enhancement-key-notexist' ) );
	}

	function test_wp_dep() : void {
		$dependency = new Dependency( uniqid( 'test-enhancement-script-notexist' ), true );
		$this->assertFalse( $dependency->wp_dep() );

		$dependency = new Dependency( uniqid( 'test-enhancement-style-notexist' ), false );
		$this->assertFalse( $dependency->wp_dep() );

		$dependency = new Dependency( 'jquery-core', true );
		$wp_dep = wp_scripts()->registered['jquery-core'];
		$this->assertEquals( $wp_dep, $dependency->wp_dep() );

		$dependency = new Dependency( 'admin-bar', false );
		$wp_dep = wp_styles()->registered['admin-bar'];
		$this->assertEquals( $wp_dep, $dependency->wp_dep() );
	}

	function test_is() : void {
		$dependency = new Dependency( uniqid( 'test-enhancement-dependency' ), true );
		$this->assertFalse( $dependency->is( 'registered' ) );
		$this->assertFalse( $dependency->is( 'enqueued' ) );

		$dependency = new Dependency( uniqid( 'test-enhancement-dependency' ), false );
		$this->assertFalse( $dependency->is( 'registered' ) );
		$this->assertFalse( $dependency->is( 'enqueued' ) );

		$dependency = new Dependency( 'jquery-core', true );
		$this->assertTrue(  $dependency->is( 'registered' ) );
		$this->assertFalse( $dependency->is( 'enqueued'   ) );

		$dependency = new Dependency( 'admin-bar', false );
		$this->assertTrue(  $dependency->is( 'registered' ) );
		$this->assertFalse( $dependency->is( 'enqueued'   ) );

		wp_enqueue_style( 'admin-bar' );
		$this->assertTrue( $dependency->is( 'enqueued' ) );
	}

	function test_get_url() : void {
		$test_handle = uniqid( 'test-enhancement-script' );
		$builtin = wp_scripts()->registered['jquery-core'];

		wp_register_script( $test_handle, $builtin->src, $builtin->deps, $builtin->ver );

		$dependency = new Dependency( $test_handle, true );
		$this->assertEquals( '/wp-includes/js/jquery/jquery.min.js?ver=3.5.1', $dependency->get_url() );

		$test_handle = uniqid( 'test-enhancement-script' );
		wp_register_script( $test_handle, $builtin->src, $builtin->deps );
		$dependency = new Dependency( $test_handle, true );

		$this->assertEquals( '/wp-includes/js/jquery/jquery.min.js?ver=5.7', $dependency->get_url() );

		$test_handle = uniqid( 'test-enhancement-script' );
		wp_register_script( $test_handle, $builtin->src, $builtin->deps );
		$rand = rand( 1, 10 );
		wp_enqueue_script( $test_handle . '?test=' . $rand );
		$dependency = new Dependency( $test_handle, true );

		$this->assertEquals( '/wp-includes/js/jquery/jquery.min.js?ver=5.7&#038;test=' . $rand, $dependency->get_url() );
	}

}

?>