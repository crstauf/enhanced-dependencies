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

if ( !function_exists( 'wp_register_enhanced_script' ) ) {

	/**
	 * Register script and return Dependency object.
	 *
	 * Useful for immediate enhancement: wp_register_enhanced_script()->set( $enhancement_key )
	 *
	 * @param string $handle
	 * @param string|bool $src
	 * @param string[] $deps
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @uses wp_register_script()
	 * @uses Enhanced_Dependencies\Dependency::get()
	 * @return Enhanced_Dependencies\Dependency
	 *
	 * @todo add tests
	 */
	function wp_register_enhanced_script( string $handle, $src, $deps = array(), $ver = false, $in_footer = false ) : Enhanced_Dependencies\Dependency {
		wp_register_script( $handle, $src, $deps, $ver, $in_footer );
		return Enhanced_Dependencies\Dependency::get( $handle, true );
	}
}

if ( !function_exists( 'wp_enqueue_enhanced_script' ) ) {

	/**
	 * Enqueue script and return Dependency object.
	 *
	 * Useful for immediate and conditional enhancement: wp_enqueue_enhanced_script()->set( $enhancement_key )
	 *
	 * @param string $handle
	 * @param string|bool $src
	 * @param string[] $deps
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @uses wp_enqueue_script()
	 * @uses Enhanced_Dependencies\Dependency::get()
	 * @return Enhanced_Dependencies\Dependency
	 *
	 * @todo add tests
	 */
	function wp_enqueue_enhanced_script( string $handle, $src, $deps = array(), $ver = false, $in_footer = false ) : Enhanced_Dependencies\Dependency {
		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
		return Enhanced_Dependencies\Dependency::get( $handle, true );
	}
}

if ( !function_exists( 'wp_register_enhanced_style' ) ) {

	/**
	 * Register stylesheet and return Dependency object.
	 *
	 * Useful for immediate enhancement: wp_register_enhanced_style()->set( $enhancement_key )
	 *
	 * @param string $handle
	 * @param string|bool $src
	 * @param string[] $deps
	 * @param string|bool|null $ver
	 * @param string $media
	 * @uses wp_register_style()
	 * @uses Enhanced_Dependencies\Dependency::get()
	 * @return Enhanced_Dependencies\Dependency
	 *
	 * @todo add tests
	 */
	function wp_register_enhanced_style( string $handle, $src, $deps = array(), $ver = false, $media = 'all' ) : Enhanced_Dependencies\Dependency {
		wp_register_style( $handle, $src, $deps, $ver, $media );
		return Enhanced_Dependencies\Dependency::get( $handle, false );
	}
}

if ( !function_exists( 'wp_enqueue_enhanced_style' ) ) {

	/**
	 * Enqueue stylesheet and return Dependency object.
	 *
	 * Useful for immediate enhancement: wp_enqueue_enhanced_script()->set( $enhancement_key )
	 *
	 * @param string $handle
	 * @param string|bool $src
	 * @param string[] $deps
	 * @param string|bool|null $ver
	 * @param string $media
	 * @uses wp_enqueue_style()
	 * @uses Enhanced_Dependencies\Dependency::get()
	 * @return Enhanced_Dependencies\Dependency
	 *
	 * @todo add tests
	 */
	function wp_enqueue_enhanced_style( string $handle, $src, $deps = array(), $ver = false, $media = 'all' ) : Enhanced_Dependencies\Dependency {
		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
		return Enhanced_Dependencies\Dependency::get( $handle, false );
	}
}

?>