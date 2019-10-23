<?php
/**
 * Main class of the plugin
 *
 * @package RVOLA\WOO\CAO
 **/

namespace RVOLA\WOO\CAO;

use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CAO
 *
 * @package RVOLA\WOO\CAO
 */
class CAO {

	/**
	 * Cron event name.
	 */
	const CRON_EVENT = 'woo_cao_cron';

	/**
	 * Storage in the class of gateways
	 *
	 * @var gateways.
	 */
	private $gateways;

	/**
	 * CAO constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 10, 1 );
		$this->add_field_gateways();
		$this->add_event_cron();
	}

	/**
	 * Adds control fields in the gateway.
	 * Hook available: 'woo_cao-gateways' / Adds a payment gateway for the control.
	 */
	private function add_field_gateways() {

		$gateways_default = array(
			'cheque',
			'bacs',
		);

		$this->gateways = apply_filters( 'woo_cao_gateways', $gateways_default );
		if ( $this->gateways && is_array( $this->gateways ) ) {
			foreach ( $this->gateways as $gateway ) {
				add_filter( 'woocommerce_settings_api_form_fields_' . $gateway, array( $this, 'add_fields' ), 10, 1 );
			}
		}
	}

	/**
	 * Add the spot in the WordPress cron.
	 */
	private function add_event_cron() {

		if ( ! wp_next_scheduled( self::CRON_EVENT ) ) {
			wp_schedule_event(
				strtotime( 'yesterday 0 hours' ), 'hourly', self::CRON_EVENT
			);
		}
		add_action( self::CRON_EVENT, array( $this, 'check_order' ), 10 );
	}

	/**
	 * Use when the extension is disabled to clean the cron spot.
	 */
	public static function clean_cron() {

		wp_clear_scheduled_hook( self::CRON_EVENT );
	}

	/**
	 * Main method that tracks options and orders pending payment.
	 * If the elements match (activation for the gateway, lifetime, command on hold), the system will cancel the command if it exceeds its time.
	 */
	public function check_order() {

		global $wpdb;

		if ( $this->gateways ) {
			foreach ( $this->gateways as $gateway ) {
				$options = get_option( 'woocommerce_' . $gateway . '_settings' );
				if (
					isset( $options ) && is_array( $options )
					&& isset( $options['woocao_enabled'] )
					&& 'yes' === $options['woocao_enabled']
				) {

					// Calculate time, depending on the mode
					$mode = isset( $options['woocao_mode'] ) ? esc_html( $options['woocao_mode'] ) : 'daily';
					switch ( $mode ) {
						case 'daily':
							$old_date = strtotime( 'today -' . $options['woocao_days'] . ' days' );
							break;

						case 'hourly':
							$old_date = current_time( 'timestamp' ) - ( $options['woocao_hours'] * HOUR_IN_SECONDS );
							break;

					}
					$old_date        = apply_filters( 'woo_cao_date_order', $old_date, $gateway, $mode );
					$old_date_format = date( 'Y-m-d H:i:s', $old_date );

					// Status to cancel
					$woo_status = $this->woo_status();

					$orders = $wpdb->get_results(
						$wpdb->prepare(
							"
							SELECT posts.ID
							FROM $wpdb->posts as posts
							INNER JOIN $wpdb->postmeta as meta
							ON posts.ID = meta.post_id
							WHERE posts.post_type = 'shop_order'
							AND posts.post_status IN ('$woo_status')
							AND posts.post_date < %s
							AND meta.meta_key = '_payment_method'
							AND meta.meta_value = %s
						",
							$old_date_format,
							$gateway
						)
					);
					if ( $orders ) {
						foreach ( $orders as $order ) {
							// Cancel order.
							$this->cancel_order( $order->ID );
						}
						wp_cache_flush();
					}
				}
			}
		}
	}

	/**
	 * Cancel the order.
	 *
	 * @param int $order_id order ID.
	 */
	private function cancel_order( $order_id ) {
		$order = new WC_Order( $order_id );

		$order->update_status(
			'cancelled',
			__( 'Cancellation of the order because payment not received at time.', 'woo-cancel-abandoned-order' )
		);
		do_action( 'woo_cao_cancel_order', $order_id );

	}

	private function woo_status() {
		$woo_status            = array();
		$woo_status_authorized = array(
			'pending',
			'on-hold',
			'processing',
			'completed',
			'refunded',
			'failed'
		);

		$default_status = apply_filters( 'woo_cao_statustocancel', array( 'on-hold' ) );

		if ( $default_status && is_array( $default_status ) ) {
			foreach ( $default_status as $status ) {
				if ( in_array( $status, $woo_status_authorized ) ) {
					$woo_status[] = sprintf( 'wc-%s', $status );
				}
			}
		}

		if ( empty( $woo_status ) ) {
			$woo_status[] = 'wc-on-hold';
		}

		$woo_status = implode( "','", $woo_status );

		return $woo_status;
	}

	/**
	 * Adds fields for gateways.
	 * Hook available: 'woo_cao-default_days' / Default value of the number of days for order processing.
	 *
	 * @param array $fields options.
	 *
	 * @return array
	 */
	public function add_fields( $fields ) {

		$new_fields = array(
			'woocao'         => array(
				'title'       => esc_html__( 'WooCommerce Cancel Abandoned Order', 'woo-cancel-abandoned-order' ),
				'type'        => 'title',
				'description' => '',
				'default'     => '',
			),
			'woocao_enabled' => array(
				'title'       => esc_html__( 'Enable/Disable', 'woo-cancel-abandoned-order' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Activation the automatic cancellation of orders.', 'woo-cancel-abandoned-order' ),
				'default'     => 'no',
				'description' => esc_html__( 'Enable this option to automatically cancel all "on Hold" orders that you have not received payment for.', 'woo-cancel-abandoned-order' ),
			),
			'woocao_mode'    => array(
				'title'   => esc_html__( 'Mode', 'woo-cancel-abandoned-order' ),
				'type'    => 'select',
				'label'   => esc_html__( 'Activation the automatic cancellation of orders.', 'woo-cancel-abandoned-order' ),
				'default' => 'daily',
				'options' => array(
					'hourly' => esc_html__( 'Hourly', 'woo-cancel-abandoned-order' ),
					'daily'  => esc_html__( 'Daily', 'woo-cancel-abandoned-order' )
				),
				'class'   => 'woo_cao-field-mode',
			),
			'woocao_hours'   => array(
				'title'       => esc_html__( 'Lifetime in hour', 'woo-cancel-abandoned-order' ),
				'type'        => 'number',
				'description' => esc_html__( 'Enter the number of hours (whole number) during which the system must consider a "pending" command as canceled.', 'woo-cancel-abandoned-order' ),
				'default'     => apply_filters( 'woo_cao_default_hours', '1' ),
				'placeholder' => esc_html__( 'days', 'woo-cancel-abandoned-order' ),
				'class'       => 'woo_cao-field-hourly woo_cao-field-moded',
			),
			'woocao_days'    => array(
				'title'       => esc_html__( 'Lifetime in days', 'woo-cancel-abandoned-order' ),
				'type'        => 'number',
				'description' => esc_html__( 'Enter the number of days that the system must consider a "on Hold" order as canceled.', 'woo-cancel-abandoned-order' ),
				'default'     => apply_filters( 'woo_cao_default_days', '15' ),
				'placeholder' => esc_html__( 'days', 'woo-cancel-abandoned-order' ),
				'class'       => 'woo_cao-field-daily woo_cao-field-moded',
			),
		);

		return array_merge( $fields, $new_fields );

	}

	/**
	 * Load assets CSS & JS
	 */
	public function assets( $hook ) {
		if ( 'woocommerce_page_wc-settings' == $hook ) {
			wp_enqueue_style( 'woo_cao', plugins_url( 'assets/woo_cao.css', WOOCAO_FILE ), null, WOOCAO_VERSION, 'all' );
			wp_enqueue_script( 'woo_cao', plugins_url( 'assets/woo_cao.js', WOOCAO_FILE ), array( 'jquery' ), WOOCAO_VERSION, true );
		}
	}
}
