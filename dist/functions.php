<?php
/**
 * Function for enhanced dependencies.
 */

defined( 'WPINC' ) || die();

if ( !function_exists( 'wp_enhance_script' ) ) {

	/**
	 * Enhance registered script.
	 *
	 * @param string $handle
	 * @param string $enhancement_key
	 * @param array $options
	 * @return void
	 */
	function wp_enhance_script( string $handle, string $enhancement_key, array $options = array() ) : void {
		Enhanced_Dependencies\Dependency::get( $handle, true )
			->set( $enhancement_key, $options );
	}

}


if ( !function_exists( 'wp_enhance_style' ) ) {

	/**
	 * Enhance registered stylesheet.
	 *
	 * @param string $handle
	 * @param string $enhancement_key
	 * @param array $options
	 * @return void
	 */
	function wp_enhance_style( string $handle, string $enhancement_key, array $options = array() ) : void {
		Enhanced_Dependencies\Dependency::get( $handle, false )
			->set( $enhancement_key, $options );
	}

}

if ( !function_exists( 'wp_dehance_scripts' ) ) {

	/**
	 * Dehance registered script.
	 *
	 * @param string $handle
	 * @param string $enhancement_key
	 * @return void
	 */
	function wp_dehance_script( string $handle, string $enhancement_key = null ) : void {
		Enhanced_Dependencies\Dependency::get( $handle, true )
			->remove( $enhancement_key );
	}

}

if ( !function_exists( 'wp_dehance_style' ) ) {

	/**
	 * Dehance registered stylesheet.
	 *
	 * @param string $handle
	 * @param string $enhancement_key
	 * @return void
	 */
	function wp_dehance_style( string $handle, string $enhancement_key = null ) : void {
		Enhanced_Dependencies\Dependency::get( $handle, false )
			->remove( $enhancement_key );
	}

}

?>