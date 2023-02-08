<?php
/**
 * Query Monitor collector.
 */

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

/**
 * @todo add action for removed enhancement
 */
class Collector extends \QM_Collector {

	protected $data = array(
		'assets' => array(),
	);

	public function __construct() {
		parent::__construct();

		if ( ! self::enabled() ) {
			return;
		}

		add_action( 'set_dependency_enhancement', array( $this, 'action__set_dependency_enhancement' ), 10, 4 );
		add_action( 'removed_dependency_enhancement', array( $this, 'action__removed_dependency_enhancement' ), 10, 3 );
	}

	public function process() : void {
	}

	public function action__set_dependency_enhancement( string $enhancement_key, array $options, string $handle, bool $is_script ) : void {
		$compare = $is_script ? 'scripts' : 'styles';

		if ( $this->get_dependency_type() !== $compare ) {
			return;
		}

		if ( ! array_key_exists( $handle, $this->data['assets'] ) ) {
			$this->data['assets'][ $handle ] = array();
		}

		$this->data['assets'][ $handle ][ $enhancement_key ] = $options;
	}

	public function action__removed_dependency_enhancement( string $enhancement_key, string $handle, bool $is_script ) : void {
		$compare = $is_script ? 'scripts' : 'styles';

		if ( $this->get_dependency_type() !== $compare ) {
			return;
		}

		unset( $this->data['assets'][ $handle ][ $enhancement_key ] );

		if ( ! empty( $this->data['assets'][ $handle ] ) ) {
			return;
		}

		unset( $this->data['assets'][ $handle ] );
	}

}