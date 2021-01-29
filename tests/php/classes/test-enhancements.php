<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Enhancements;

class Test_Enhancements extends \WP_UnitTestCase {

	/**
	 * @covers \Enhanced_Dependencies\Enhancements::get()
	 */
	function test_get() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_class_name = uniqid( 'test_enhancement_class_name' );

		$this->assertIsArray( Enhancements::get() );
		$this->assertEquals( '', Enhancements::get( $test_enhancement_key ) );

		Enhancements::register( $test_enhancement_key, $test_enhancement_class_name );
		$this->assertEquals( array( $test_enhancement_key => $test_enhancement_class_name ), Enhancements::get() );
		$this->assertEquals( $test_enhancement_class_name, Enhancements::get( $test_enhancement_key ) );
	}

	/**
	 * @covers \Enhanced_Dependencies\Enhancements::register()
	 */
	function test_register() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_class_name = uniqid( 'test_enhancement_class_name' );

		$this->assertEquals( '', Enhancements::get( $test_enhancement_key ) );
		Enhancements::register( $test_enhancement_key, $test_enhancement_class_name );
		$this->assertEquals( $test_enhancement_class_name, Enhancements::get( $test_enhancement_key ) );
	}

	function test_register__exists() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_class_name = uniqid( 'test_enhancement_class_name' );

		Enhancements::register( $test_enhancement_key, $test_enhancement_class_name );
		@Enhancements::register( $test_enhancement_key, $test_enhancement_class_name . '-2' );
		$this->assertEquals( $test_enhancement_class_name, Enhancements::get( $test_enhancement_key ) );
	}

}