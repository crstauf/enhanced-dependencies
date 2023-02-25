<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Enhancements\Async;

class Test_Enhancement_Async extends \WP_UnitTestCase {

	function test_script() : void {
		$actual = Async::apply( "<script src=''></script>", uniqid(), true );
		$this->assertEquals( $actual, "<script async src=''></script>" );
	}

	function test_stylesheet() : void {
		$actual = trim( Async::apply( "<link href='' media='all' />", uniqid(), false ) );
		$this->assertEquals( "<link href='' media='print' onload='this.media=\"all\"' />", $actual );

		ob_start();
		@do_action( 'wp_footer' );
		$footer = ob_get_clean();

		$this->assertEquals( sprintf( '<noscript>%1$s<link href=\'\' media=\'all\' />%1$s</noscript>%1$s', PHP_EOL ), $footer );
	}

}

?>