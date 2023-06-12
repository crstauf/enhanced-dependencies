<?php
/**
 * Enhanced Dependencies integration with Query Monitor.
 */

namespace Enhanced_Dependencies\Query_Monitor;
use Enhanced_Dependencies\Plugin;
use Enhanced_Dependencies\Enhancements_Manager;

defined( 'WPINC' ) || die(); // @codeCoverageIgnore

class Integrate {

	const COLLECTOR = 'enhanced_dependencies';

	/**
	 * Initialize.
	 *
	 * @uses static::instance()
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	public static function init() : void {
		static $once = false;

		if ( $once ) {
			return;
		}

		$once = true;

		static::instance();
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

		if ( is_null( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {
		add_filter( 'qm/collectors', array( $this, 'filter__qm_collectors' ) );
		add_filter( 'qm/output/panel_menus', array( $this, 'filter__qm_output_panel_menus' ) );
		add_filter( 'qm/outputter/html', array( $this, 'filter__qm_outputter_html' ), 80 );

		add_filter( 'qm/collect/concerned_actions/assets_scripts', array( $this, 'filter__qm_collect_concerned_actions' ) );
		add_filter( 'qm/collect/concerned_actions/assets_styles', array( $this, 'filter__qm_collect_concerned_actions' ) );
	}

	/**
	 * Filter: qm/collectors
	 *
	 * Register Query Monitor collectors.
	 *
	 * @param \QM_Collector[] $collectors
	 * @return \QM_Collector[]
	 */
	public function filter__qm_collectors( array $collectors ) : array {
		require_once Plugin::directory_path() . 'query-monitor/classes/Collector.php';
		require_once Plugin::directory_path() . 'query-monitor/classes/Data.php';
		require_once Plugin::directory_path() . 'query-monitor/collectors/scripts.php';
		require_once Plugin::directory_path() . 'query-monitor/collectors/styles.php';

		$collectors['enhanced_scripts'] = new Collector_Scripts;
		$collectors['enhanced_styles']  = new Collector_Styles;

		return $collectors;
	}

	/**
	 * Filter: qm/output/panel_menus
	 *
	 * Add 'Enhanced' child tab to 'Scripts' and 'Styles' tab.
	 *
	 * @param mixed[] $panel_menus
	 * @return mixed[]
	 */
	public function filter__qm_output_panel_menus( array $panel_menus ) : array {
		foreach ( array( 'qm-assets_scripts', 'qm-assets_styles' ) as $tab ) {
			if ( ! array_key_exists( 'children', $panel_menus[ $tab ] ) ) {
				$panel_menus[ $tab ]['children'] = array();
			}
		}

		$collector = \QM_Collectors::get( 'enhanced_scripts' );
		if ( ! is_null( $collector ) ) {
			$panel_menus['qm-assets_scripts']['children']['qm-enhanced_scripts'] = array(
				'title' => sprintf( 'Enhanced (%d)', count( $collector->get_data()->assets ) ),
				'href'  => '#qm-enhanced_scripts',
			);
		}

		$collector = \QM_Collectors::get( 'enhanced_styles' );
		if ( ! is_null( $collector ) ) {
			$panel_menus['qm-assets_styles']['children']['qm-enhanced_styles'] = array(
				'title' => sprintf( 'Enhanced (%d)', count( $collector->get_data()->assets ) ),
				'href'  => '#qm-enhanced_styles',
			);
		}

		return $panel_menus;
	}

	/**
	 * Filter: qm/outputter/html
	 *
	 * Register Query Monitor outputters.
	 *
	 *
	 * @param \QM_Output[] $outputters
	 * @return \QM_Output[]
	 */
	public function filter__qm_outputter_html( array $outputters ) : array {
		require_once Plugin::directory_path() . 'query-monitor/classes/Output.php';
		require_once Plugin::directory_path() . 'query-monitor/output/scripts.php';
		require_once Plugin::directory_path() . 'query-monitor/output/styles.php';

		$collector = \QM_Collectors::get( 'enhanced_scripts' );
		if ( ! is_null( $collector ) ) {
			$outputters['enhanced_scripts'] = new Output_Html_Scripts( $collector );
		}

		$collector = \QM_Collectors::get( 'enhanced_styles' );
		if ( ! is_null( $collector ) ) {
			$outputters['enhanced_styles'] = new Output_Html_Styles( $collector );
		}

		return $outputters;
	}

	/**
	 * Filters: qm/collect/concerned_actions/{$id}
	 *
	 * Add actions to Concerned Actions lists for scripts and styles.
	 *
	 * @param string[] $actions
	 * @uses Enhanced_Dependencies\Enhancements_Manager::get()
	 * @return string[]
	 */
	public function filter__qm_collect_concerned_actions( array $actions ) : array {
		$actions[] = 'include_dependency_enhancements';
		$actions[] = 'set_dependency_enhancement';
		$actions[] = 'removed_dependency_enhancement';

		$enhancements = ( array ) Enhancements_Manager::get();

		foreach ( array_keys( $enhancements ) as $key ) {
			$actions[] = 'set_dependency_enhancement_' . $key;
			$actions[] = 'removed_dependency_enhancement_' . $key;
		}

		return $actions;
	}

}

Integrate::init();