<?php

namespace Enhanced_Dependencies\Tests;
use Enhanced_Dependencies\Dependency;

defined( 'WPINC' ) || die();

trait Utilities {

	protected function _test_dependency_data( \WP_Dependencies $helper, string $handle, string $enhancement_key = null, array $options = null ) : void {
		if ( is_null( $options ) ) {
			$value = $helper->get_data( $handle, Dependency::EXTRA_DATA_KEY );

			if ( !is_null( $enhancement_key ) ) {
				$this->assertArrayNotHasKey( $enhancement_key, $value->enhancements );
				return;
			}

			$this->assertFalse( $value );
			return;
		}

		$dependency_enhancements = $helper->get_data( $handle, Dependency::EXTRA_DATA_KEY );

		$this->assertInstanceOf(  Dependency::class, $dependency_enhancements );
		$this->assertArrayHasKey( $enhancement_key,  $dependency_enhancements->enhancements );
		$this->assertEquals(      $options,          $dependency_enhancements->enhancements[ $enhancement_key ] );
	}

}

?>