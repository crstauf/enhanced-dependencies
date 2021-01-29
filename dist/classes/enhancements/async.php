<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Async
 */
class Async extends Enhancement {

	const KEY = 'async';

	/**
	 * Add asynchronous enhancement.
	 *
	 * For scripts, add the "async" attribute.
	 * For stylesheets, change "media" attribute and add "onload" event.
	 *
	 * @link https://www.filamentgroup.com/lab/load-css-simpler/
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @param array $options
	 * @return string
	 */
	static function apply( string $tag, string $handle, bool $is_script, array $options = array() ) : string {
		if ( $is_script )
			return str_replace( '<script ', '<script async ', $tag );

		$enhanced = str_replace( 'media=\'all\'', 'media=\'print\' onload=\'this.media="all"\'', $tag );
		return $enhanced . '<noscript>' . trim( $tag ) . '</noscript>' . PHP_EOL;
	}

}

Async::register();

?>