<?php
/*
 * Output for Query Monitor for enhanced scripts.
 */

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

class Output_Html_Styles extends Output_Html {

	const TYPE = 'styles';

	public function name() {
		return __( 'Enhanced', 'query-monitor' );
	}

	public function get_type_labels() {
		return array(
			/* translators: %s: Total number of enhanced styles */
			'total'  => _x( 'Total: %s', 'Enhanced styles', 'query-monitor' ),
			'plural' => __( 'Styles', 'query-monitor' ),
			/* translators: %s: Total number of enhanced styles */
			'count'  => _x( 'Styles (%s)', 'Enhanced styles', 'query-monitor' ),
		);
	}

}

?>