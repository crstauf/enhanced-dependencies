<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Enhancements\Defer;

class Test_Enhancement_Defer extends \WP_UnitTestCase {

	function test_script() : void {
		$actual = Defer::apply( "<script src=''></script>", uniqid(), true );
		$this->assertEquals( $actual, "<script defer src=''></script>" );
	}

	function test_stylesheet() : void {
		$actual = trim( Defer::apply( "<link href='' media='all' />", uniqid(), false ) );
		$this->assertEquals( "<link href='' media='all' />", $actual );
	}

}

?>