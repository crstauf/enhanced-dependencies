<?php
/**
 * Query Monitor collector for enhanced scripts.
 */

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

class Collector_Scripts extends Collector {

	/**
	 * @var string
	 */
	public $id = 'enhanced_scripts';

	/**
	 * @return string
	 */
	public function get_dependency_type() : string {
		return 'scripts';
	}

	public function get_storage(): \QM_Data {
		return new Data();
	}

}
