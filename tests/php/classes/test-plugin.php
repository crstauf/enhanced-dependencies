<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Dependency;

defined( 'WPINC' ) || die();

class Test_Plugin extends \WP_UnitTestCase {

	function test_paths() : void {
		$this->assertIsString( Plugin::directory_path() );
		$this->assertIsString( Plugin::file() );
	}

	function test_hooks() : void {
		$this->assertTrue( has_action( 'set_dependency_enhancement_push' ) );
		$this->assertTrue( has_filter( 'script_loader_tag' ) );
		$this->assertTrue( has_filter(  'style_loader_tag' ) );
	}

	function test_includes() : void {
		$included_files = get_included_files();

		foreach ( array(
			'classes/dependency.php',
			'classes/enhancement.php',
			'classes/enhancements-manager.php',
			'classes/enhancements/async.php',
			'classes/enhancements/defer.php',
			'classes/enhancements/inline.php',
			'classes/enhancements/preconnect.php',
			'classes/enhancements/prefetch.php',
			'classes/enhancements/preload.php',
			'classes/plugin.php',
			'enhanced-dependencies.php',
			'functions.php',
		) as $file )
			$this->assertContains( Plugin::directory_path() . $file, $included_files );

		# Enhancement dns-prefetch is handled by WordPress.
		$this->assertNotContains( Plugin::directory_path() . 'classes/enhancements/dns-prefetch.php', $included_files );
	}

	function test_set_dependency_enhancement_push() : void {
		$dependency = Dependency::get( 'jquery-core', true );

		$this->assertFalse( $dependency->has( 'preload' ) );

		$dependency->set( 'push' );
		$this->assertTrue( $dependency->has( 'preload' ) );
	}

}

?>