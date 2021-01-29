<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Enhancements_Manager;

class Test_Enhancements extends \WP_UnitTestCase {

	/**
	 * @covers \Enhanced_Dependencies\Enhancements_Manager::get()
	 */
	function test_get() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_class_name = uniqid( 'test_enhancement_class_name' );

		$this->assertIsArray( Enhancements_Manager::get() );
		$this->assertEquals( '', Enhancements_Manager::get( $test_enhancement_key ) );

		Enhancements_Manager::register( $test_enhancement_key, $test_enhancement_class_name );
		$this->assertEquals( $test_enhancement_class_name, Enhancements_Manager::get( $test_enhancement_key ) );
	}

	/**
	 * @covers \Enhanced_Dependencies\Enhancements_Manager::register()
	 */
	function test_register() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_class_name = uniqid( 'test_enhancement_class_name' );

		$this->assertEquals( '', Enhancements_Manager::get( $test_enhancement_key ) );
		Enhancements_Manager::register( $test_enhancement_key, $test_enhancement_class_name );
		$this->assertEquals( $test_enhancement_class_name, Enhancements_Manager::get( $test_enhancement_key ) );
	}

	function test_register__exists() : void {
		$test_enhancement_key = uniqid( 'test-enhancement-key' );
		$test_enhancement_class_name = uniqid( 'test_enhancement_class_name' );

		Enhancements_Manager::register( $test_enhancement_key, $test_enhancement_class_name );
		@Enhancements_Manager::register( $test_enhancement_key, $test_enhancement_class_name . '-2' );
		$this->assertEquals( $test_enhancement_class_name, Enhancements_Manager::get( $test_enhancement_key ) );
	}

}