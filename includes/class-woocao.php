<?php
/**
 * Main class of the plugin
 *
 * @package RVOLA
 **/

namespace RVOLA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WooCAO
 *
 * @package RVOLA
 */
class WooCAO {

	/**
	 * Cron event name.
	 */
	const CRON_EVENT = 'woo_cao_cron';

	/**
	 * Singleton
	 *
	 * @var singleton.
	 */
	private static $_singleton = null;
	/**
	 * Storage in the class of gateways
	 *
	 * @var gateways.
	 */
	private $gateways;

	/**
	 * WooCAO constructor.
	 */
	public function __construct() {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			add_action( 'init', array( $this, 'load_languages' ), 10 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_action( 'admin_print_styles', array( $this, 'style' ), 10 );
			$this->add_event_cron();
			$this->add_field_gateways();

		}
	}

	/**
	 * Add the spot in the WordPress cron.
	 */
	private function add_event_cron() {

		if ( ! wp_next_scheduled( self::CRON_EVENT ) ) {
			wp_schedule_event(
				strtotime( 'yesterday 0 hours' ), 'daily', self::CRON_EVENT
			);
		}
		add_action( self::CRON_EVENT, array( $this, 'cancel_order' ), 10 );
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

		$this->gateways = apply_filters_deprecated( 'woo_cao-gateways', array( $gateways_default ), '1.2', 'woo_cao_gateways', 'It will be replaced on version 1.3' );
		if ( $this->gateways && is_array( $this->gateways ) ) {
			foreach ( $this->gateways as $gateway ) {
				add_filter( 'woocommerce_settings_api_form_fields_' . $gateway, array( $this, 'add_fields' ), 10, 1 );
			}
		}
	}

	/**
	 * Singleton.
	 *
	 * @return mixed
	 */
	public static function instance() {

		if ( is_null( self::$_singleton ) ) {
			$class            = __CLASS__;
			self::$_singleton = new $class();
		}

		return self::$_singleton;
	}

	/**
	 * Use when the extension is disabled to clean the cron spot.
	 */
	public static function desactivation() {

		wp_clear_scheduled_hook( self::CRON_EVENT );
	}

	/**
	 * Load language files.
	 */
	public function load_languages() {

		load_plugin_textdomain( 'woo-cancel-abandoned-order', false, dirname( __FILE__ ) . '/languages' );
	}

	/**
	 * Add links in the list of plugins.
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 *
	 * @return mixed
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( 'woo-cancel-abandoned-order/woo-cancel-abandoned-order.php' ) === $plugin_file ) {
			array_push(
				$plugin_meta,
				sprintf(
					'<a href="https://www.paypal.me/rvola" target="_blank">%s</a>',
					__( 'Donate', 'woo-cancel-abandoned-order' )
				)
			);
		}

		return $plugin_meta;
	}

	/**
	 * Main method that tracks options and orders pending payment.
	 * If the elements match (activation for the gateway, lifetime, command on hold), the system will cancel the command if it exceeds its time.
	 */
	public function cancel_order() {

		global $wpdb;

		if ( $this->gateways ) {
			foreach ( $this->gateways as $gateway ) {
				$options = get_option( 'woocommerce_' . $gateway . '_settings' );
				if (
					isset( $options ) && is_array( $options )
					&& isset( $options['woocao_enabled'] )
					&& 'yes' === $options['woocao_enabled']
					&& isset( $options['woocao_days'] )
					&& ! empty( $options['woocao_days'] )
				) {

					$old_date        = strtotime( 'today -' . $options['woocao_days'] . ' days' );
					$old_date_format = date( 'Y-m-d 00:00:00', $old_date );

					$orders_id = $wpdb->get_results(
						$wpdb->prepare(
							"
							SELECT posts.ID
							FROM $wpdb->posts as posts
							INNER JOIN $wpdb->postmeta as meta
							ON posts.ID = meta.post_id
							WHERE posts.post_type = 'shop_order'
							AND posts.post_status = 'wc-on-hold'
							AND posts.post_date < %s
							AND meta.meta_key = '_payment_method'
							AND meta.meta_value = %s
						",
							$old_date_format,
							$gateway
						)
					);
					if ( $orders_id ) {
						foreach ( $orders_id as $order_id ) {
							$order = new \WC_Order( $order_id->ID );
							$order->update_status(
								'cancelled',
								__( 'Cancellation of the order because payment not received at time.', 'woo-cancel-abandoned-order' )
							);
						}
						wp_cache_flush();
					}
				}
			}
		}
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
				'title'       => __( 'WooCommerce Cancel Abandoned Order', 'woo-cancel-abandoned-order' ),
				'type'        => 'title',
				'description' => '',
				'default'     => '',
			),
			'woocao_enabled' => array(
				'title'       => __( 'Enable/Disable', 'woo-cancel-abandoned-order' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activation the automatic cancellation of orders.', 'woo-cancel-abandoned-order' ),
				'default'     => 'no',
				'description' => __( 'Enable this option to automatically cancel all "on Hold" orders that you have not received payment for.', 'woo-cancel-abandoned-order' ),
			),
			'woocao_days'    => array(
				'title'       => __( 'Lifetime ', 'woo-cancel-abandoned-order' ),
				'type'        => 'number',
				'description' => __( 'Enter the number of days that the system must consider a "on Hold" order as canceled.', 'woo-cancel-abandoned-order' ),
				'default'     => apply_filters_deprecated( 'woo_cao-default_days', array( '15' ), '1.2', 'woo_cao_default_days', 'It will be replaced on version 1.3' ),
				'placeholder' => __( 'days', 'woo-cancel-abandoned-order' ),
				'class'       => 'woo_cao-field-days',
			),
		);

		return array_merge( $fields, $new_fields );

	}

	/**
	 * Field style.
	 */
	public function style() {

		echo '<style type="text/css">';
		echo '.woocommerce table.form-table .regular-input.woo_cao-field-days {width:70px}';
		echo '</style>';
	}

}
