<?php
/*
 * Output for Query Monitor for enhanced scripts.
 */

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

class Output_Html_Scripts extends Output_Html {

	/**
	 * @var string
	 */
	const TYPE = 'scripts';

	/**
	 * @return string
	 */
	public function name() : string {
		return __( 'Enhanced', 'query-monitor' );
	}

	/**
	 * @return array<string, string>
	 */
	public function get_type_labels() : array {
		return array(
			/* translators: %s: Total number of enhanced scripts */
			'total'  => _x( 'Total: %s', 'Enhanced scripts', 'query-monitor' ),
			'plural' => __( 'Scripts', 'query-monitor' ),
			/* translators: %s: Total number of enhanced scripts */
			'count'  => _x( 'Enhanced (%s)', 'Enhanced scripts', 'query-monitor' ),
		);
	}

}