<?php
/*
 * Output for Query Monitor for enhanced scripts.
 */

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

class Output_Html_Scripts extends Output_Html {

	const TYPE = 'scripts';

	public function name() {
		return __( 'Enhanced', 'query-monitor' );
	}

	public function get_type_labels() {
		return array(
			/* translators: %s: Total number of enhanced scripts */
			'total'  => _x( 'Total: %s', 'Enhanced scripts', 'query-monitor' ),
			'plural' => __( 'Scripts', 'query-monitor' ),
			/* translators: %s: Total number of enhanced scripts */
			'count'  => _x( 'Enhanced (%s)', 'Enhanced scripts', 'query-monitor' ),
		);
	}

}

?>