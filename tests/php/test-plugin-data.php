<?php

namespace Enhanced_Dependencies\Tests;

defined( 'WPINC' ) || die();

class Plugin_Data extends \WP_UnitTestCase {

	function test_plugin_data() : void {
		$expected = array(
			'Name' => 'Enhanced Dependencies',
			'PluginURI' => 'https://github.com/crstauf/enhanced-dependencies',
			'Version' => '1.0',
			'Description' => 'Collection of enhancements for WordPress dependencies.',
			'Author' => 'Caleb Stauffer',
			'AuthorURI' => 'https://develop.calebstauffer.com',
			'TextDomain' => '',
			'DomainPath' => '',
			'Network' => false,
			'RequiresWP' => '5.6',
			'RequiresPHP' => '7.4',
			'Title' => 'Enhanced Dependencies',
			'AuthorName' => 'Caleb Stauffer',
		);

		$actual = get_plugin_data( \Enhanced_Dependencies\Plugin::file(), false );
		$this->assertSame( $expected, $actual );
	}

}