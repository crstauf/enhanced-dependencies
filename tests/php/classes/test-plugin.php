<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;

defined( 'WPINC' ) || die();

class Test_Plugin extends \WP_UnitTestCase {

	function test_paths() : void {
		$this->assertIsString( Plugin::directory() );
		$this->assertIsString( Plugin::file() );
	}

	function test_includes() : void {
		$included_files = get_included_files();

		foreach ( array(
			'functions.php',
			'classes/dependency.php',
		) as $file )
			$this->assertContains( Plugin::directory() . $file, $included_files );
	}

}

?>