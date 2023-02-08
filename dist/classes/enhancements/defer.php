<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Defer
 */
class Defer extends Enhancement {

	const KEY = 'defer';

	/**
	 * Add defer enhancement to script tags.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	public static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
		if ( ! $is_script ) {
			return $tag; // @codeCoverageIgnore
		}

		return str_replace( '<script src=', '<script defer src=', $tag );
	}

}

Defer::register();