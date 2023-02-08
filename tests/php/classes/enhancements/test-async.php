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
		$this->assertEquals( "<link href='' media='print' onload='this.media=\"all\"' /><noscript><link href='' media='all' /></noscript>", $actual );
	}

}

?>