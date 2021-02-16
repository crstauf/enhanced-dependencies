<?php

namespace Enhanced_Dependencies\Enhancements;
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
	static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
		$path = static::get_dependency_path( $handle, $is_script );

		if ( empty( $path ) )
			return $tag;

		$content = file_get_contents( $path );

		if ( false === $content ) {
			trigger_error( sprintf( 'Unable to get contents of <code>%s</code> at <code>%s</code>.', $handle, $path ) ); // @codeCoverageIgnore
			return $tag; // @codeCoverageIgnore
		}

		if ( empty( $content ) )
			$content = '/* empty */';

		if ( $is_script )
			return '<script id="' . esc_attr( $handle ) . '-inline-js">' . $content . '</script>';

		return '<style id="' . esc_attr( $handle ) . '-inline-css">' . $content . '</style>';
	}

	/**
	 * Get dependency path.
	 *
	 * @param string $handle
	 * @param bool $is_script
	 * @return string
	 */
	protected static function get_dependency_path( string $handle, bool $is_script ) : string {
		$helper = $is_script ? wp_scripts() : wp_styles();
		$dependency = $helper->registered[ $handle ];

		$path = str_replace( trailingslashit( site_url() ), trailingslashit( ABSPATH ), $dependency->src );
		$path = apply_filters( 'enhanced-dependencies/dependency/path', $path, $handle, $is_script );

		if ( !file_exists( $path ) ) {
			trigger_error( sprintf( 'Unable to find <code>%s</code> %s file at <code>%s</code>.', $handle, $is_script ? 'script' : 'stylesheet', $path ) );
			return '';
		}

		return $path;
	}

}

Inline::register();

?>