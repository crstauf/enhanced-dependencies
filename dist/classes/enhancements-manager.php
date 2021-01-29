<?php

namespace Enhanced_Dependencies;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements_Manager
 */
class Enhancements_Manager {

	/**
	* @var array key => class name
	*/
	protected static $enhancements = array();

	/**
	 * Get enhancement object(s).
	 *
	 * @param null|string $key
	 * @return string|array
	 */
	static function get( string $key = null ) {
		if ( is_null( $key ) )
			return static::$enhancements;

		if ( !array_key_exists( $key, static::$enhancements ) )
			return '';

		return static::$enhancements[ $key ];
	}

	/**
	 * Register enhancement.
	 *
	 * @param string $key
	 * @param string $class_name
	 * @return void
	 *
	 * @todo add check that class name exists and apply() method is callable
	 */
	static function register( string $key, string $class_name ) : void {
		if ( array_key_exists( $key, static::$enhancements ) ) {
			trigger_error( sprintf( 'Enhancement with key <code>%s</code> is already registered.', $key ) );
			return;
		}

		static::$enhancements[ $key ] = $class_name;
	}

}

?>