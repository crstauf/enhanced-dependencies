<?php

namespace Enhanced_Dependencies;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Plugin
 */
class Plugin {

	protected static $file;

	/**
	 * Initialize.
	 *
	 * @uses static::instance()
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	static function init( string $file ) : void {
		$once = false;

		if ( $once )
			return;

		static::$file = $file;

		$instance = static::instance();
	}

	/**
	 * Get instance.
	 *
	 * @return self
	 *
	 * @codeCoverageIgnore
	 */
	protected static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	/**
	 * Get plugin file.
	 *
	 * @return string
	 */
	static function file() : string {
		return static::$file;
	}

	/**
	 * Get plugin directory path.
	 *
	 * @return string
	 */
	static function directory_path() : string {
		return trailingslashit( plugin_dir_path( static::$file ) );
	}

	/**
	 * Construct.
	 *
	 * @codeCoverageIgnore
	 */
	protected function __construct() {
		$this->includes();
		$this->hooks();
	}

	/**
	 * Include plugin files.
	 *
	 * @uses static::directory()
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected function includes() : void {
		require_once static::directory_path() . 'functions.php';
		require_once static::directory_path() . 'classes/dependency.php';
		require_once static::directory_path() . 'classes/enhancements-manager.php';
		require_once static::directory_path() . 'classes/enhancement.php';

		require_once static::directory_path() . '/classes/enhancements/async.php';
		require_once static::directory_path() . '/classes/enhancements/defer.php';
		require_once static::directory_path() . '/classes/enhancements/inline.php';
		require_once static::directory_path() . '/classes/enhancements/preconnect.php';
		require_once static::directory_path() . '/classes/enhancements/prefetch.php';
		require_once static::directory_path() . '/classes/enhancements/preload.php';

		do_action( 'include_dependency_enhancements' );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected function hooks() : void {
		add_action( 'set_dependency_enhancement_push', array( $this, 'action__set_dependency_enhancement_push' ), 10, 3 );

		add_filter( 'script_loader_tag', array( $this, 'filter__script_loader_tag' ), 10, 2 );
		add_filter(  'style_loader_tag', array( $this,  'filter__style_loader_tag' ), 10, 2 );
	}

	/**
	 * Filter: script_loader_tag
	 *
	 * Maybe enhance script tags.
	 *
	 * @param string $html
	 * @param string $handle
	 * @uses static::maybe_enhance_tag()
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	function filter__script_loader_tag( string $html, string $handle ) : string {
		if ( !doing_filter( 'script_loader_tag' ) )
			return $html;

		return $this->maybe_enhance_tag( $html, $handle, true );
	}

	/**
	 * Filter: style_loader_tag
	 *
	 * Maybe enhance stylesheet tags.
	 *
	 * @param string $html
	 * @param string $handle
	 * @uses static::maybe_enhance_tag()
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	function filter__style_loader_tag( string $html, string $handle ) : string {
		if ( !doing_filter( 'style_loader_tag' ) )
			return $html;

		return $this->maybe_enhance_tag( $html, $handle, false );
	}

	/**
	 * Maybe enhance dependency tag.
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param bool $is_script
	 * @uses Dependency::get()
	 * @uses Enhancements_Manager::get()
	 * @uses Enhancement::apply()
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	protected function maybe_enhance_tag( string $tag, string $handle, bool $is_script ) : string {
		$dependency = Dependency::get( $handle, $is_script );

		if ( !$dependency->has() )
			return $tag;

		foreach ( $dependency->enhancements as $key => $options ) {
			$enhancement = Enhancements_Manager::get( $key );

			if ( empty( $enhancement ) )
				continue;

			$tag = call_user_func_array( array( $enhancement, 'apply' ), array( $tag, $handle, $is_script, $options ) );
		}

		return $tag;
	}

	/**
	 * Action: set_dependency_enhancement_push
	 *
	 * Magically handle 'push' enhancement.
	 *
	 * @param array $options
	 * @param string $handle
	 * @param bool $is_script
	 * @return void
	 */
	function action__set_dependency_enhancement_push( array $options, string $handle, bool $is_script ) : void {
		$options['link'] = false;
		$options['http_header'] = true;

		$dependency = Dependency::get( $handle, $is_script );
		$dependency->set( 'preload', $options );
	}

}

?>