<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancements\Preload;

class Test_Enhancement_Preload extends \WP_UnitTestCase {

	function test_register() : void {
		$this->assertSame( 10, has_action( 'set_dependency_enhancement', array( Preload::class, 'action__set_dependency_enhancement' ) ) );
		$this->assertSame( 10, has_action( 'send_headers', array( Preload::class, 'action__send_headers' ) ) );
		$this->assertSame(  5, has_action( 'wp_head', array( Preload::class, 'action__wp_head' ) ) );
	}

	function test_apply() : void {
		$actual = Preload::apply(  "<script src=''></script>", uniqid(), true );
		$this->assertEquals( $actual, "<script src=''></script>" );

		$actual = Preload::apply(  "<link href='' />", uniqid(), false );
		$this->assertEquals( $actual, "<link href='' />" );
	}

	function test_not_enqueued() : void {
		$handle = uniqid( 'test-script' );
		$url = trailingslashit( site_url() ) . 'wp-content/mu-plugins/enhanced-dependencies/tests/test-script.js';

		wp_register_script( $handle, $url );

		$dependency = Dependency::get( $handle, true )->set( Preload::KEY );
		$dependency->set( Preload::KEY, array( 'http_header' => false ) );

		$this->assertFalse( $dependency->is( 'enqueued' ) );

		ob_start();
		@do_action( 'wp_head' );
		$output = ob_get_clean();

		$this->assertFalse( strpos( $output, '<link rel="preload" id="' . $handle . '-preload-js" href="' . $dependency->get_url() . '" />' ) );
	}

	function test_enqueued() : void {
		$handle = uniqid( 'test-script' );
		$domain = 'http://' . uniqid( 'test-enqueued' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		wp_enqueue_script( $handle );

		$dependency = Dependency::get( $handle, true );
		$dependency->set( Preload::KEY, array( 'http_header' => false ) );

		$this->assertTrue( $dependency->is( 'enqueued' ) );

		ob_start();
		@do_action( 'wp_head' );
		$output = ob_get_clean();

		$this->assertIsInt( strpos( $output, '<link rel="preload" id="' . $handle . '-preload-js" href="' . $dependency->get_url() . '" />' ) );
	}

	function test_always() : void {
		$handle = uniqid( 'test-script' );
		$domain = 'http://' . uniqid( 'test-always' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		$dependency = Dependency::get( $handle, true );

		$dependency->set( Preload::KEY, array( 'always' => true, 'http_header' => false ) );
		$this->assertFalse( $dependency->is( 'enqueued' ) );

		ob_start();
		@do_action( 'wp_head' );
		$output = ob_get_clean();

		$this->assertIsInt( strpos( $output, '<link rel="preload" id="' . $handle . '-preload-js" href="' . $dependency->get_url() . '" />' ) );
	}

	function test_not_link() : void {
		$handle = uniqid( 'test-script' );
		$domain = 'http://' . uniqid( 'test-notlink' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		wp_enqueue_script( $handle );

		$dependency = Dependency::get( $handle, true );
		$dependency->set( Preload::KEY, array( 'link' => false ) );

		$this->assertTrue( $dependency->is( 'enqueued' ) );

		ob_start();
		@do_action( 'wp_head' );
		$output = ob_get_clean();

		$this->assertFalse( strpos( $output, '<link rel="preload" id="' . $handle . '-preload-js" href="' . $dependency->get_url() . '" />' ) );
	}

}

?>