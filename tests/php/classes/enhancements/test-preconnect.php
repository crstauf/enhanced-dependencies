<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancements\Preconnect;

class Test_Enhancement_Preconnect extends \WP_UnitTestCase {

	function test_register() : void {
		$this->assertSame( 10, has_filter( 'wp_resource_hints', array( Preconnect::class, 'filter__wp_resource_hints' ) ) );
	}

	function test_apply() : void {
		$actual = Preconnect::apply(  "<script src=''></script>", uniqid(), true );
		$this->assertEquals( $actual, "<script src=''></script>" );

		$actual = Preconnect::apply(  "<link href='' />", uniqid(), false );
		$this->assertEquals( $actual, "<link href='' />" );
	}

	function test_filter_wp_resource_hints_early_returns() : void {
		$this->assertSame( array(), Preconnect::filter__wp_resource_hints( array(), uniqid() ) );
		$this->assertSame( array(), apply_filters( 'wp_resource_hints', array(), uniqid() ) );
	}

	function test_not_enqueued() : void {
		$handle = uniqid( 'test-script-notenqueued' );
		$url = trailingslashit( site_url() ) . 'wp-content/mu-plugins/enhanced-dependencies/tests/test-script.js';

		wp_register_script( $handle, $url );
		Dependency::get( $handle, true )->set( Preconnect::KEY );

		$urls = apply_filters( 'wp_resource_hints', array(), 'preconnect' );
		$this->assertSame( array(), $urls );
	}

	function test_enqueued() : void {
		$handle = uniqid( 'test-script-enqueued' );
		$domain = 'http://' . uniqid( 'test-enqueued' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );
		Dependency::get( $handle, true )->set( Preconnect::KEY );
		wp_enqueue_script( $handle );

		$urls = apply_filters( 'wp_resource_hints', array(), 'preconnect' );
		$this->assertContains( $domain, $urls );
	}

	function test_always() : void {
		$handle = uniqid( 'test-script-always' );
		$domain = 'http://' . uniqid( 'test-always' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );

		$dependency = Dependency::get( $handle, true );
		$dependency->set( Preconnect::KEY, array( 'always' => true ) );

		$urls = apply_filters( 'wp_resource_hints', array(), 'preconnect' );

		$this->assertFalse( $dependency->is( 'enqueued' ) );
		$this->assertContains( $domain, $urls );
	}

	function test_error() : void {
		$handle = uniqid( 'test-script-error' );
		$domain = uniqid( 'test_error' ) . '.com';
		$url = $domain . '/test-script.js';

		wp_register_script( $handle, $url );

		Dependency::get( $handle, true )->set( Preconnect::KEY, array( 'always' => true ) );

		$this->expectException( \PHPUnit\Framework\Error\Error::class );
		$urls = apply_filters( 'wp_resource_hints', array(), 'preconnect' );
	}

}

?>