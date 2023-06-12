<?php

namespace Enhanced_Dependencies\Query_Monitor;

defined( 'WPINC' ) || die();

abstract class Output_Html extends \QM_Output_Html {

	const TYPE = '';

	/**
	 * Collector instance.
	 *
	 * @var Collector
	 */
	protected $collector;

	/**
	 * @return string[]
	 */
	abstract public function get_type_labels() : array;

	/**
	 * @return Collector
	 */
	public function get_collector() {
		return $this->collector;
	}

	public function output() {
		$data = $this->collector->get_data();
		$data = ( array ) $data->assets;

		if ( empty( $data ) ) {
			$this->before_non_tabular_output();

			$notice = __( sprintf( 'No enhanced %s.', static::TYPE ), 'query-monitor' );
			echo $this->build_notice( $notice ); // WPCS: XSS ok.

			$this->after_non_tabular_output();

			return;
		}

		$helper = 'scripts' === $this->get_collector()->get_dependency_type() ? 'wp_script_is' : 'wp_style_is';

		ksort( $data );

		$data_statuses = array();

		foreach ( array_keys( $data ) as $handle ) {
			foreach ( array(
				'registered',
				'enqueued',
				'to_do',
				'done',
			) as $status ) {
				if ( ! $helper( $handle, $status ) ) {
					continue;
				}

				if ( ! array_key_exists( $handle, $data_statuses ) ) {
					$data_statuses[ $handle ] = array();
				}

				$data_statuses[ $handle ][] = $status;
			}
		}

		$this->before_tabular_output();

		echo '<thead>'
			. '<tr>'
				. '<th scope="col">' . esc_html__( 'Handle', 'query-monitor' ) . '</th>'
				. '<th scope="col">' . esc_html__( 'Status', 'query-monitor' ) . '</th>'
				. '<th scope="col">' . esc_html__( 'Enhancement', 'query-monitor' ) . '</th>'
				. '<th scope="col">' . esc_html__( 'Options', 'query-monitor' ) . '</th>'
			. '</tr>'
		. '</thead>';

		echo '<tbody>';

			$row = 1;

			foreach ( $data as $handle => $enhancements ) {
				$first = true;

				foreach ( $enhancements as $key => $options ) {

					$odd = ( 0 !== ( $row++ % 2 ) );

					echo '<tr' . ( $odd ? ' class="qm-odd"' : '' ) . '>';

						if ( $first ) {
							echo '<th scope="row" class="qm-nowrap qm-ltr" rowspan="' . count( $enhancements ) . '">'
								. '<span class="qm-sticky">'
									. esc_html( $handle )
								. '</span>'
							. '</th>';

							printf( '<td class="qm-nowrap qm-ltr" rowspan="%d">%s</td>', count( $enhancements ), esc_html( implode( ' ', $data_statuses[ $handle ] ) ) );
						}

						echo '<td class="qm-nowrap qm-ltr">' . esc_html( $key ) . '</td>';
						echo '<td class="qm-ltr"><code>'
							. json_encode( $options, JSON_PRETTY_PRINT )
						. '</code></td>';

					echo '</tr>';

					$first = false;
				}
			}

		echo '</tbody>';

		echo '<tfoot>'
			. '<tr>';

				printf(
					'<td colspan="4">%s</td>',
					sprintf(
						esc_html( $this->get_type_labels()['total'] ),
						'<span clas="qm-items-number">' . esc_html( number_format_i18n( count( $data ) ) ) . '</span>'
					)
				);

			echo '</tr>'
		. '</tfoot>';

		$this->after_tabular_output();
	}

}