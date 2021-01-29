<?php

namespace Enhanced_Dependencies;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Abstract class: Enhanced_Dependencies\Enhancement
 */
abstract class Enhancement {

	/**
	 * @var string Identification key.
	 */
	const KEY = '';

	/**
	 * @var array Default options.
	 */
	const DEFAULT_ARGS = array();

	/**
	 * Register enhancement with manager.
	 *
	 * @uses Enhancements_Manager::register()
	 * @return void
	 */
	static function register() : void {
		Enhancements_Manager::register( static::KEY, static::class );
	}

	/**
	 * Apply enhancement.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	abstract static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string;

}

?>