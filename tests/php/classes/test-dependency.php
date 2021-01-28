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

}

?>