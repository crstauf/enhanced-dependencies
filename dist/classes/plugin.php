<?php

namespace Enhanced_Dependencies;

defined( 'WPINC' ) || die();

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
	 * Get plugin file path.
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
	static function directory() : string {
		return plugin_dir_path( static::$file );
	}

	/**
	 * Construct.
	 *
	 * @codeCoverageIgnore
	 */
	protected function __construct() {
		$this->includes();
	}

	/**
	 * Include plugin files.
	 *
	 * @uses static::directory()
	 * @return void
	 */
	protected function includes() : void {
		require_once static::directory() . 'functions.php';
	}

}