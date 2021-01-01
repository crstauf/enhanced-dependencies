<?php

namespace Enhanced_Dependencies;

class Dependency {

	const EXTRA_DATA_KEY = '_enhancements';

	protected $handle;
	protected $is_script;
	protected $enhancements = array();

	static function get( string $handle, bool $is_script ) : self {
		$helper = $is_script ? wp_scripts() : wp_styles();

		if ( !$helper->query( $handle ) )
			return new self;

		$enhancements = $helper->get_data( $handle, static::EXTRA_DATA_KEY );

		if ( !empty( $enhancements ) )
			return $enhancements;

		$enhancements = new self( $handle, $is_script );
		$enhancements->save();

		return $enhancements;
	}

	function __construct( string $handle = '', bool $is_script = false ) {
		$this->handle = $handle;
		$this->is_script = $is_script;
	}

	function __get( string $property ) {
		return $this->$property;
	}

	protected function helper() : \WP_Dependencies {
		return $this->is_script ? wp_scripts() : wp_styles();
	}

	protected function save() : void {
		if ( empty( $this->handle ) )
			return;

		$helper = $this->helper();
		$helper->add_data( $this->handle, static::EXTRA_DATA_KEY, $this );
	}

	function add( string $enhancement_key, array $options = array() ) : self {
		if ( empty( $this->handle ) )
			return $this;

		$this->enhancements[ $enhancement_key ] = $options;
		$this->save();

		return $this;
	}

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