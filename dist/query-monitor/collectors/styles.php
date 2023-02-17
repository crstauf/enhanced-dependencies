<?php
/**
 * Query Monitor collector for enhanced styles.
 */

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

class Collector_Styles extends Collector {

	/**
	 * @var string
	 */
	public $id = 'enhanced_styles';

	/**
	 * @return string
	 */
	public function get_dependency_type() : string {
		return 'styles';
	}

	public function get_storage(): \QM_Data {
		return new Data();
	}

}