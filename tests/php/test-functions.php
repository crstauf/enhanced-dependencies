<?php

namespace Enhanced_Dependencies\Tests;

class Test_Functions extends \WP_UnitTestCase {
	use Utilities;

	function test_functions_defined() : void {
		foreach ( array(
			'wp_enhance_script',
			'wp_dehance_script',
			'wp_enhance_style',
			'wp_dehance_style',
		) as $function_name )
			$this->assertTrue( function_exists( $function_name ) );
	}

	function test_wp_enhance_script() : void {
		$test_handle = uniqid( 'test-enhancement-script' );
		$builtin = wp_scripts()->registered['jquery-core'];

		wp_register_script( $test_handle, $builtin->src, $builtin->deps, $builtin->ver );

		$this->_test_dependency_data( wp_scripts(), $test_handle );

		$test_enhancement_key = 'test-script-enhancement';
		$options = array( 'test-opt' => rand() );
		wp_enhance_script( $test_handle, $test_enhancement_key, $options );

		$this->_test_dependency_data( wp_scripts(), $test_handle, $test_enhancement_key, $options );
	}

	function test_wp_enhance_style() : void {
		$test_handle = uniqid( 'test-enhancement-style' );
		$builtin = wp_styles()->registered['admin-bar'];

		wp_register_style( $test_handle, $builtin->src, $builtin->deps, $builtin->ver );

		$this->_test_dependency_data( wp_styles(), $test_handle );

		$test_enhancement_key = 'test-style-enhancement';
		$options = array( 'test-opt' => rand() );
		wp_enhance_style( $test_handle, $test_enhancement_key, $options );

		$this->_test_dependency_data( wp_styles(), $test_handle, $test_enhancement_key, $options );
	}

	function test_wp_dehance_script() : void {
		$test_handle = uniqid( 'test-dehancement-script' );
		$test_enhancement_key = 'test-script-enhancement';
		$options = array( 'test-opt' => rand() );
		$builtin = wp_scripts()->registered['jquery-core'];

		wp_register_script( $test_handle, $builtin->src, $builtin->deps, $builtin->ver );
		 wp_enhance_script( $test_handle, $test_enhancement_key, $options );

		$this->_test_dependency_data( wp_scripts(), $test_handle, $test_enhancement_key, $options );

		wp_dehance_script( $test_handle, $test_enhancement_key );
		$this->_test_dependency_data( wp_scripts(), $test_handle, $test_enhancement_key );
	}

	function test_wp_dehance_style() : void {
		$test_handle = uniqid( 'test-dehancement-style' );
		$test_enhancement_key = 'test-style-enhancement';
		$options = array( 'test-opt' => rand() );
		$builtin = wp_styles()->registered['admin-bar'];

		wp_register_style( $test_handle, $builtin->src, $builtin->deps, $builtin->ver );
		 wp_enhance_style( $test_handle, $test_enhancement_key, $options );

		$this->_test_dependency_data( wp_styles(), $test_handle, $test_enhancement_key, $options );

		wp_dehance_style( $test_handle, $test_enhancement_key );
		$this->_test_dependency_data( wp_styles(), $test_handle, $test_enhancement_key );
	}

	function test_wp_register_enhanced_script() : void {
		$test_handle = uniqid( 'test-enhancement-script' );
		$builtin = wp_scripts()->registered['jquery-core'];
		$test_enhancement_key = 'test-script-enhancement';
		$options = array( 'test-opt' => rand() );

		wp_register_enhanced_script( $test_handle, $builtin->src, $builtin->deps, $builtin->ver )->set( $test_enhancement_key, $options );

		$this->assertTrue( wp_script_is( $test_handle, 'registered' ) );
		$this->_test_dependency_data( wp_scripts(), $test_handle, $test_enhancement_key, $options );
	}

	function test_wp_enqueue_enhanced_script() : void {
		$test_handle = uniqid( 'test-enhancement-script' );
		$builtin = wp_scripts()->registered['jquery-core'];
		$test_enhancement_key = 'test-script-enhancement';
		$options = array( 'test-opt' => rand() );

		wp_enqueue_enhanced_script( $test_handle, $builtin->src, $builtin->deps, $builtin->ver )->set( $test_enhancement_key, $options );

		$this->assertTrue( wp_script_is( $test_handle, 'enqueued' ) );
		$this->_test_dependency_data( wp_scripts(), $test_handle, $test_enhancement_key, $options );
	}

	function test_wp_register_enhanced_style() : void {
		$test_handle = uniqid( 'test-enhancement-style' );
		$builtin = wp_styles()->registered['admin-bar'];
		$test_enhancement_key = 'test-style-enhancement';
		$options = array( 'test-opt' => rand() );

		wp_register_enhanced_style( $test_handle, $builtin->src, $builtin->deps, $builtin->ver )->set( $test_enhancement_key, $options );

		$this->assertTrue( wp_style_is( $test_handle, 'registered' ) );
		$this->_test_dependency_data( wp_styles(), $test_handle, $test_enhancement_key, $options );
	}

	function test_wp_enqueue_enhanced_style() : void {
		$test_handle = uniqid( 'test-enhancement-style' );
		$builtin = wp_styles()->registered['admin-bar'];
		$test_enhancement_key = 'test-style-enhancement';
		$options = array( 'test-opt' => rand() );

		wp_enqueue_enhanced_style( $test_handle, $builtin->src, $builtin->deps, $builtin->ver )->set( $test_enhancement_key, $options );

		$this->assertTrue( wp_style_is( $test_handle, 'enqueued' ) );
		$this->_test_dependency_data( wp_styles(), $test_handle, $test_enhancement_key, $options );
	}

}

?>