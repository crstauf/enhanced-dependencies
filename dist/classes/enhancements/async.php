<?php

namespace Enhanced_Dependencies\Enhancements;
use Enhanced_Dependencies\Enhancement;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Enhancements\Async
 */
class Async extends Enhancement {

	const KEY = 'async';

	protected static $footer_queue = array();

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
			return str_replace( '<script src=', '<script async src=', $tag );

		$id = sprintf( '%s-css', esc_attr( $handle ) );
		$noscript = str_replace( $id, $id . '-noscript', $tag );
		$enhanced = str_replace( 'media=\'all\'', 'media=\'print\' onload=\'this.media="all"\'', $tag );

		static::$footer_queue[ $handle ] = trim( $noscript );

		return $enhanced;
	}

	/**
	 * Action: wp_print_footer_scripts
	 *
	 * Print unenhanced stylesheet tags for noscript context.
	 *
	 * @return void
	 */
	static function action__wp_print_footer_scripts() : void {
		if ( 'wp_print_footer_scripts' !== current_action() )
			return;

		if ( empty( static::$footer_queue ) )
			return;

		echo '<noscript>' . PHP_EOL;

		foreach ( static::$footer_queue as $tag )
			echo $tag . PHP_EOL;

		echo '</noscript>' . PHP_EOL;
	}

}

Async::register();

add_action( 'wp_print_footer_scripts', array( Async::class, 'action__wp_print_footer_scripts' ) );

?>
