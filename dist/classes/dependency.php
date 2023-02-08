<?php
/**
 * Object to manage a dependency's data and enhancements.
 */

namespace Enhanced_Dependencies;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

/**
 * Class: Enhanced_Dependencies\Dependency
 */
class Dependency {

	/**
	 * @var string Key for dependency extra data.
	 */
	const EXTRA_DATA_KEY = '_enhancements';

	/**
	 * @var string $handle Dependency handle.
	 * @var bool $is_script Dependency is a script.
	 * @var array $enhancements Array of enhancements options.
	 */
	protected $handle;
	protected $is_script;
	protected $enhancements = array();

	/**
	 * Get this object from dependency data.
	 *
	 * @param string $Handle
	 * @param bool $is_script
	 * @return self
	 */
	public static function get( string $handle, bool $is_script ) : self {
		$helper = $is_script ? wp_scripts() : wp_styles();

		if ( ! $helper->query( $handle ) ) {
			return new self;
		}

		$enhancements = $helper->get_data( $handle, static::EXTRA_DATA_KEY );

		if ( is_a( $enhancements, static::class ) ) {
			return $enhancements;
		}

		$enhancements = new self( $handle, $is_script );
		$enhancements->save();

		return $enhancements;
	}


	/**
	 * Construct.
	 *
	 * @param string $handle
	 * @param bool $is_script
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct( string $handle = '', bool $is_script = false ) {
		$this->handle    = $handle;
		$this->is_script = $is_script;
	}


	/**
	 * Getter.
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function __get( string $property ) {
		return $this->$property;
	}

	/**
	 * Get dependencies helper.
	 *
	 * @return WP_Scripts|WP_Styles
	 */
	public function helper() : \WP_Dependencies {
		return $this->is_script ? wp_scripts() : wp_styles();
	}

	/**
	 * Save to dependency extra data.
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	protected function save() : void {
		if ( empty( $this->handle ) ) {
			return;
		}

		$helper = $this->helper();
		$helper->add_data( $this->handle, static::EXTRA_DATA_KEY, $this );
	}

	/**
	 * Add enhancement to dependency.
	 *
	 * @param string $enhancement_key
	 * @param array $options
	 * @return self
	 */
	public function set( string $enhancement_key, array $options = array() ) : self {
		if ( empty( $this->handle ) ) {
			return $this;
		}

		$this->enhancements[ $enhancement_key ] = $options;
		$this->save();

		do_action( 'set_dependency_enhancement', $enhancement_key, $options, $this->handle, $this->is_script );
		do_action( 'set_dependency_enhancement_' . $enhancement_key, $options, $this->handle, $this->is_script );

		return $this;
	}

	/**
	 * Remove enhancement from dependency.
	 *
	 * @param null|string $enhancement_key
	 * @return self
	 */
	public function remove( string $enhancement_key = null ) : self {
		if ( empty( $this->handle ) ) {
			return $this;
		}

		if ( is_null( $enhancement_key ) ) {
			$this->enhancements = array();
			$this->save();

			return $this;
		}

		unset( $this->enhancements[ $enhancement_key ] );
		$this->save();

		do_action( 'removed_dependency_enhancement', $enhancement_key, $this->handle, $this->is_script );
		do_action( 'removed_dependency_enhancement_' . $enhancement_key, $this->handle, $this->is_script );

		return $this;
	}

	/**
	 * Check dependency has enhancement(s).
	 *
	 * @param null|string $enhancement_key
	 * @return bool
	 */
	public function has( string $enhancement_key = null ) : bool {
		if ( is_null( $enhancement_key ) ) {
			return ! empty( $this->enhancements );
		}

		return array_key_exists( $enhancement_key, $this->enhancements );
	}

	/**
	 * Get WordPress dependency object.
	 *
	 * @return bool|_WP_Dependency
	 */
	public function wp_dep() {
		if (
			$this->is_script
			&& ! wp_script_is( $this->handle, 'registered' )
		) {
			return false;
		}

		if (
			! $this->is_script
			&& ! wp_style_is( $this->handle, 'registered' )
		) {
			return false;
		}

		return $this->helper()->registered[ $this->handle ];
	}

	/**
	 * Check if dependency is registered, enqueued, etc.
	 *
	 * @param string $action
	 * @uses wp_script_is()
	 * @uses wp_style_is()
	 * @return bool
	 */
	public function is( string $action ) : bool {
		if ( $this->is_script ) {
			return wp_script_is( $this->handle, $action );
		}

		return wp_style_is( $this->handle, $action );
	}

	/**
	 * Get dependency's URL.
	 *
	 * @param bool $absolute Convert relative URL to absolute.
	 * @return string
	 */
	public function get_url( bool $absolute = true ) : string {
		$object = $this->wp_dep();
		$helper = $this->helper();

		$src = $object->src;

		if (
			$absolute
			&& $helper->in_default_dir( $src )
		) {
			$src = $helper->base_url . $src;
		}

		$ver = $object->ver;

		if ( ! is_null( $ver ) && empty( $ver ) )
			$ver = $helper->default_version;
		}

		if ( isset( $helper->args[ $this->handle ] ) ) {
			$ver  = $ver ? $ver . '&#038;' : '';
			$ver .= $helper->args[ $this->handle ];
		}

		$src = add_query_arg( 'ver', $ver, $src );

		return urldecode_deep( $src );
	}

}
