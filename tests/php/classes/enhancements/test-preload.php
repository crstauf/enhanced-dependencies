<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancements\Preload;

class Test_Enhancement_Preload extends \WP_UnitTestCase {

	function test_register() : void {
		$this->assertSame( 10, has_action( 'set_dependency_enhancement', array( Preload::class, 'action__set_dependency_enhancement' ) ) );
		$this->assertSame( 10, has_action( 'send_headers', array( Preload::class, 'action__send_headers' ) ) );
		$this->assertSame( 10, has_filter( 'wp_resource_hints', array( Preload::class, 'filter__wp_resource_hints' ) ) );
	}

	function test_apply() : void {
		$actual = Preload::apply(  "<script src=''></script>", uniqid(), true );
		$this->assertEquals( $actual, "<script src=''></script>" );

		$actual = Preload::apply(  "<link href='' />", uniqid(), false );
		$this->assertEquals( $actual, "<link href='' />" );
	}

	function test_filter_wp_resource_hints_early_returns() : void {
		$this->assertSame( array(), Preload::filter__wp_resource_hints( array(), uniqid() ) );
		$this->assertSame( array(), apply_filters( 'wp_resource_hints', array(), uniqid() ) );
	}

	function test_not_enqueued() : void {
		$handle = uniqid( 'test-script' );
		$url = trailingslashit( site_url() ) . 'wp-content/mu-plugins/enhanced-dependencies/tests/test-script.js';

		wp_register_script( $handle, $url );
		Dependency::get( $handle, true )->set( Preload::KEY );

		$urls = apply_filters( 'wp_resource_hints', array(), 'preload' );
		$this->assertSame( array(), $urls );
	}

	function test_enqueued() : void {
		$handle = uniqid( 'test-script' );
		$domain = 'http://' . uniqid( 'test-enqueued' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		wp_enqueue_script( $handle );

		$urls = apply_filters( 'wp_resource_hints', array(), 'preload' );
		$this->assertNotContains( $url, $urls );

		Dependency::get( $handle, true )->set( Preload::KEY );

		$urls = apply_filters( 'wp_resource_hints', array(), 'preload' );
		$this->assertContains( $url, $urls );
	}

	function test_always() : void {
		$handle = uniqid( 'test-script' );
		$domain = 'http://' . uniqid( 'test-always' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		$dependency = Dependency::get( $handle, true );

		$dependency->set( Preload::KEY, array( 'always' => true ) );
		$urls = apply_filters( 'wp_resource_hints', array(), 'preload' );

		$this->assertFalse( $dependency->is( 'enqueued' ) );
		$this->assertContains( $url, $urls );
	}

	function test_not_link() : void {
		$handle = uniqid( 'test-script' );
		$domain = 'http://' . uniqid( 'test-notlink' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		$dependency = Dependency::get( $handle, true );

		$dependency->set( Preload::KEY, array( 'link' => false ) );
		$urls = apply_filters( 'wp_resource_hints', array(), 'preload' );

		$this->assertNotContains( $url, $urls );
	}

}

?>