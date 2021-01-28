<?php
/**
 * Object to manage a dependency's data and enhancements.
 */

namespace Enhanced_Dependencies;

defined( 'WPINC' ) || die();

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
	static function get( string $handle, bool $is_script ) : self {
		$helper = $is_script ? wp_scripts() : wp_styles();

		if ( !$helper->query( $handle ) )
			return new self;

		$enhancements = $helper->get_data( $handle, static::EXTRA_DATA_KEY );

		if ( is_a( $enhancements, static::class ) )
			return $enhancements;

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
	function __construct( string $handle = '', bool $is_script = false ) {
		$this->handle = $handle;
		$this->is_script = $is_script;
	}


	/**
	 * Getter.
	 *
	 * @param string $property
	 * @return mixed
	 */
	function __get( string $property ) {
		return $this->$property;
	}

	/**
	 * Get dependencies helper.
	 *
	 * @return WP_Scripts|WP_Styles
	 *
	 * @codeCoverageIgnore
	 */
	protected function helper() : \WP_Dependencies {
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
		if ( empty( $this->handle ) )
			return;

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
	function set( string $enhancement_key, array $options = array() ) : self {
		if ( empty( $this->handle ) )
			return $this;

		$this->enhancements[ $enhancement_key ] = $options;
		$this->save();

		return $this;
	}

	/**
	 * Remove enhancement from dependency.
	 *
	 * @param null|string $enhancement_key
	 * @return self
	 */
	function remove( string $enhancement_key = null ) : self {
		if ( empty( $this->handle ) )
			return $this;

		if ( is_null( $enhancement_key ) ) {
			$this->enhancements = array();
			$this->save();

			return $this;
		}

		unset( $this->enhancements[ $enhancement_key ] );
		$this->save();

		return $this;
	}

}

?>