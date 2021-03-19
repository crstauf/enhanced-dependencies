<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Preload
 *
 * Supported parameters:
 * - always: boolean to preload regardless of dependency status
 * - http_header: attempt to set in HTTP headers
 * - link: attempt to set in <head> block
 */
class Preload extends Enhancement {

	const KEY = 'preload';

	/**
	 * @var array Dependencies with preload enhancements.
	 */
	protected static $dependencies = array(
		'scripts' => array(),
		'styles'  => array(),
	);

	/**
	 * Add filter callback.
	 *
	 * @uses Enhanced_Dependencies\Enhancement
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	static function register() : void {
		parent::register();

		add_action( 'set_dependency_enhancement', array( static::class, 'action__set_dependency_enhancement' ), 10, 4 );
		add_action( 'send_headers',               array( static::class, 'action__send_headers' ) );
		add_filter( 'wp_resource_hints',          array( static::class, 'filter__wp_resource_hints' ), 10, 2 );
	}

	/**
	 * Action: set_dependency_enhancement
	 *
	 * @param string $enhancement_key
	 * @param array $options
	 * @param string $handle
	 * @param bool $is_script
	 * @uses static::add_dependency()
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	static function action__set_dependency_enhancement( string $enhancement_key, array $options, string $handle, bool $is_script ) : void {
		if ( 'set_dependency_enhancement' !== current_action() )
			return;

		if ( static::KEY !== $enhancement_key )
			return;

		if ( did_action( 'wp_head' ) ) {
			trigger_error( sprintf( 'Too late to apply <code>%s</code> enhancement to <code>%s</code> %s dependency.', static::class, $handle, $is_script ? 'script' : 'style' ) );
			return;
		}

		static::add_dependency( $handle, $is_script );
	}

	/**
	 * Action: send_headers
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	static function action__send_headers() : void {
		if ( 'send_headers' !== current_action() )
			return;

		foreach ( static::$dependencies as $dep_type => $handles ) {
			foreach ( $handles as $index => $handle ) {
				$dependency = Dependency::get( $handle, 'scripts' === $dep_type );

				if (
					array_key_exists( 'http_header', $dependency->enhancements[static::KEY] )
					&& false === $dependency->enhancements[static::KEY]['http_header']
				)
					continue;

				if (
					empty( $dependency->enhancements[static::KEY]['always'] )
					&& !$dependency->is( 'enqueued' )
				)
					continue;

				$header = sprintf( 'Link: <%s>; rel=preload; as=%s',
					$dependency->get_url(),
					'scripts' === $dep_type ? 'script' : 'style'
				);

				header( $header, false );
				unset( static::$dependencies[ $dep_type ][ $index ] );
			}
		}
	}

	/**
	 * Add dependency to local storage.
	 *
	 * @param string $handle
	 * @param bool $is_script
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected static function add_dependency( string $handle, bool $is_script ) : void {
		$key = $is_script ? 'scripts' : 'styles';
		static::$dependencies[ $key ][] = $handle;
	}

	/**
	 * Do nothing.. preload uses a separate method.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
		return $tag;
	}

	/**
	 * Filter: wp_resource_hints
	 *
	 * Add preload link tag.
	 *
	 * @param array $urls
	 * @param string $type
	 * @return array
	 */
	static function filter__wp_resource_hints( array $urls, string $type ) : array {
		if ( 'wp_resource_hints' !== current_filter() )
			return $urls;

		if ( 'preload' !== $type )
			return $urls;

		foreach ( static::$dependencies as $dep_type => $handles ) {
			foreach ( $handles as $handle ) {
				$dependency = Dependency::get( $handle, 'scripts' === $dep_type );

				if (
					array_key_exists( 'link', $dependency->enhancements[static::KEY] )
					&& false === $dependency->enhancements[static::KEY]['link']
				)
					continue;

				if (
					empty( $dependency->enhancements[static::KEY]['always'] )
					&& !$dependency->is( 'enqueued' )
				)
					continue;

				$urls[] = $dependency->wp_dep()->src;
			}
		}

		return $urls;
	}

}

Preload::register();

?>