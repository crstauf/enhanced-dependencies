<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Prefetch
 *
 * Supported parameters:
 * - always: boolean to prefetch regardless of dependency status
 */
class Prefetch extends Enhancement {

	const KEY = 'prefetch';

	/**
	 * @var array Dependencies with prefetch enhancements.
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
	public static function register() : void {
		parent::register();

		add_action( 'set_dependency_enhancement', array( static::class, 'action__set_dependency_enhancement' ), 10, 4 );
		add_filter( 'wp_resource_hints', array( static::class, 'filter__wp_resource_hints' ), 10, 2 );
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
	public static function action__set_dependency_enhancement( string $enhancement_key, array $options, string $handle, bool $is_script ) : void {
		if ( 'set_dependency_enhancement' !== current_action() ) {
			return;
		}

		if ( static::KEY !== $enhancement_key ) {
			return;
		}

		if ( did_action( 'wp_head' ) ) {
			trigger_error( sprintf(
				'Too late to apply <code>%s</code> enhancement to <code>%s</code> %s dependency.',
				static::class,
				$handle,
				$is_script ? 'script' : 'style'
			) );

			return;
		}

		static::add_dependency( $handle, $is_script );
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
	 * Do nothing.. prefetch uses a separate tag.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	public static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
		return $tag;
	}

	/**
	 * Filter: wp_resource_hints
	 *
	 * Add prefetch link tag.
	 *
	 * @param array $urls
	 * @param string $type
	 * @return array
	 */
	public static function filter__wp_resource_hints( array $urls, string $type ) : array {
		if ( 'wp_resource_hints' !== current_filter() ) {
			return $urls;
		}

		if ( 'prefetch' !== $type ) {
			return $urls;
		}

		foreach ( static::$dependencies as $dep_type => $handles ) {
			foreach ( $handles as $handle ) {
				$dependency = Dependency::get( $handle, 'scripts' === $dep_type );

				if (
					empty( $dependency->enhancements[ static::KEY ]['always'] )
					&& ! $dependency->is( 'enqueued' )
				) {
					continue;
				}

				$urls[] = $dependency->wp_dep()->src;
			}
		}

		return $urls;
	}

}

Prefetch::register();