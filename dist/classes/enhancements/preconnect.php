<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Preconnect
 */
class Preconnect extends Enhancement {

	const KEY = 'preconnect';

	/**
	 * @var array Dependencies with preconnect enhancements.
	 */
	protected static $dependencies = array();

	/**
	 * Action: set_dependency_enhancement
	 *
	 * @param string $enhancement_key
	 * @param array $options
	 * @param string $handle
	 * @param bool $is_script
	 * @uses static::add_dependency()
	 * @return void
	 */
	static function action__set_dependency_enhancement( string $enhancement_key, array $options, string $handle, bool $is_script ) : void {
		if ( !doing_action( 'set_dependency_enhancement' ) )
			return;

		if ( static::KEY !== $enhancement_key )
			return;

		static::add_dependency( $handle, $is_script );
	}

	/**
	 * Add dependency to local storage.
	 *
	 * @param string $handle
	 * @param bool $is_script
	 * @return void
	 */
	static function add_dependency( string $handle, bool $is_script ) : void {
		$key = $is_script ? 'scripts' : 'styles';
		static::$dependencies[ $key ] = $handle;
	}

	/**
	 * Add preconnect enhancement to script tags.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
	}

}

Preconnect::register();

add_action( 'set_dependency_enhancement', array( Preconnect::class, 'action__set_dependency_enhancement' ), 10, 4 );
