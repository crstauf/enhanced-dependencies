<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Dependency;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Inline
 */
class Inline extends Enhancement {

	const KEY = 'inline';

	/**
	 * Print dependency inline.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	public static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
		$path = static::get_dependency_path( $handle, $is_script );

		if ( empty( $path ) ) {
			return $tag;
		}

		$content = file_get_contents( $path );

		if ( false === $content ) {
			trigger_error( sprintf( 'Unable to get contents of <code>%s</code> at <code>%s</code>.', $handle, $path ) ); // @codeCoverageIgnore
			return $tag; // @codeCoverageIgnore
		}

		if (
			'production' !== wp_get_environment_type()
			&& empty( $content )
		) {
			$content = '/* empty */';
		}

		if ( empty( $content ) ) {
			return ''; // @codeCoverageIgnore
		}

		if ( $is_script ) {
			return '<script id="' . esc_attr( $handle ) . '-inline-js">' . $content . '</script>';
		}

		return '<style id="' . esc_attr( $handle ) . '-inline-css">' . $content . '</style>';
	}

	/**
	 * Get dependency path.
	 *
	 * @param string $handle
	 * @param bool $is_script
	 * @return string
	 *
	 * @todo move into Dependency class
	 * @todo add test for external dependency check
	 */
	protected static function get_dependency_path( string $handle, bool $is_script ) : string {
		$dependency = Dependency::get( $handle, $is_script );

		$path = str_replace( trailingslashit( site_url() ), trailingslashit( ABSPATH ), $dependency->wp_dep()->src );

		if ( $dependency->helper()->in_default_dir( $dependency->wp_dep()->src ) ) {
			$path = ABSPATH . $path;
		}

		$path = apply_filters( 'enhanced-dependencies/dependency/path', $path, $handle, $is_script );

		if ( ! file_exists( $path ) ) {
			trigger_error( sprintf(
				'Unable to find <code>%s</code> %s file at <code>%s</code>.',
				$handle,
				$is_script ? 'script' : 'stylesheet',
				$path
			) );

			return '';
		}

		return $path;
	}

}

Inline::register();