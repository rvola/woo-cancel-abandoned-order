<?php
/*
Plugin Name:		    WooCommerce Cancel Abandonned Order
Plugin URI:			    https://github.com/rvola/woocommerce-cancel-abandonned-order

Description:		    Cancel "on hold" orders after a certain number of days

Version:			    1.0.0
Revision:			    2017-10-28
Creation:               2017-10-28

Author:				    studio RVOLA
Author URI:			    https://www.rvola.com

Text Domain:		    woo_ca_i18n
Domain Path:		    /languages

Requires at least:      4.0
Tested up to:           4.9
Requires PHP:           5.3
WC requires at least:   2.2.0
WC tested up to:        3.2.0

License:                GNU General Public License v3.0
License URI:            https://www.gnu.org/licenses/gpl-3.0.html
*/

namespace RVOLA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCAO {

	/**
	 * Cron event name
	 */
	const CRON_EVENT = 'woo_cao_cron';
	/**
	 * Text Domain
	 */
	const LANG = 'woo_cao_i18n';

	/**
	 * @var singleton
	 */
	private static $singleton = null;
	/**
	 * @var gateways
	 */
	private $gateways;

	/**
	 * WooCAO constructor.
	 */
	public function __construct() {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			add_action( 'init', array( $this, 'loadLanguages' ), 10 );
			add_filter( 'plugin_row_meta', array( $this, 'pluginRowMeta' ), 10, 2 );
			add_action( 'admin_print_styles', array( $this, 'style' ), 10 );
			$this->addEventCron();
			$this->addFieldGateways();

		}
	}

	/**
	 * Add the spot in the WordPress cron.
	 */
	private function addEventCron() {

		if ( ! wp_next_scheduled( self::CRON_EVENT ) ) {
			wp_schedule_event( strtotime( 'yesterday 0 hours' ), 'daily', self::CRON_EVENT );
		}
		add_action( self::CRON_EVENT, array( $this, 'cancelOrder' ), 10 );
	}

	/**
	 * Adds control fields in the gateway.
	 * Hook available: 'woo_cao-gateways' / Adds a payment gateway for the control.
	 *
	 */
	private function addFieldGateways() {

		$gateways_default = array(
			'cheque',
			'bacs',
		);

		$this->gateways = apply_filters( 'woo_cao-gateways', $gateways_default );
		if ( $this->gateways && is_array( $this->gateways ) ) {
			foreach ( $this->gateways as $gateway ) {
				add_filter( 'woocommerce_settings_api_form_fields_' . $gateway, array( $this, 'addFields' ), 10, 1 );
			}
		}
	}

	/**$
	 * Singleton
	 * @return mixed
	 */
	public static function load() {

		if ( is_null( self::$singleton ) ) {
			$class           = __CLASS__;
			self::$singleton = new $class;
		}

		return self::$singleton;
	}

	/**
	 * Use when the extension is disabled to clean the cron spot
	 */
	public static function desactivation() {

		wp_clear_scheduled_hook( self::CRON_EVENT );
	}

	/**
	 * Load language files
	 */
	public function loadLanguages() {

		load_plugin_textdomain( self::LANG, false, dirname( __FILE__ ) . '/languages' );
	}

	/**
	 * Add links in the list of plugins
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function pluginRowMeta( $links, $file ) {
		if ( $file === plugin_basename( __FILE__ ) ) {
			array_push(
				$links,
				sprintf(
					'<a href="https://www.paypal.me/rvola" target="_blank">%s</a>',
					__( 'Donate', self::LANG )
				),
				sprintf(
					'<a href="https://github.com/rvola/woocommerce-cancel-abandonned-order" target="_blank">GitHub</a>',
					__( 'GitHub', self::LANG )
				)
			);
		}

		return $links;
	}

	/**
	 * Main method that tracks options and orders pending payment.
	 * If the elements match (activation for the gateway, lifetime, command on hold), the system will cancel the command if it exceeds its time.
	 */
	public function cancelOrder() {

		global $wpdb;

		if ( $this->gateways ) {
			foreach ( $this->gateways as $gateway ) {
				$options = get_option( 'woocommerce_' . $gateway . '_settings' );
				if (
					isset( $options ) && is_array( $options )
					&& isset( $options['woocao_enabled'] ) && $options['woocao_enabled'] === 'yes'
					&& isset( $options['woocao_days'] ) && ! empty( $options['woocao_days'] )
				) {

					$old_date        = strtotime( 'today -' . $options['woocao_days'] . ' days' );
					$old_date_format = date( 'Y-m-d 00:00:00', $old_date );

					$orders_id = $wpdb->get_results(
						$wpdb->prepare( "
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
								__( 'Cancellation of the order because payment not received at time.', self::LANG )
							);
						}
					}
				}
			}
		}
	}

	/**
	 * Adds fields for gateways
	 * Hook available: 'woo_cao-default_days' / Default value of the number of days for order processing.
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function addFields( $fields ) {

		$new_fields = array(
			'woocao'         => array(
				'title'       => __( 'WooCommerce Cancel Abandonned Order', self::LANG ),
				'type'        => 'title',
				'description' => '',
				'default'     => '',
			),
			'woocao_enabled' => array(
				'title'       => __( 'Enable/Disable', self::LANG ),
				'type'        => 'checkbox',
				'label'       => __( 'Activation the automatic cancellation of orders.', self::LANG ),
				'default'     => 'no',
				'description' =>
					__( 'Enable this option to automatically cancel all "on Hold" orders that you have not received payment for.', self::LANG ),
			),
			'woocao_days'    => array(
				'title'       => __( 'Lifetime ', self::LANG ),
				'type'        => 'number',
				'description' => __( 'Enter the number of days that the system must consider a "on Hold" order as canceled.', self::LANG ),
				'default'     => apply_filters( 'woo_cao-default_days', 15 ),
				'placeholder' => __( 'days', self::LANG ),
				'class'       => 'woo_cao-field-days'
			),
		);

		return array_merge( $fields, $new_fields );

	}

	/**
	 * Field style
	 */
	public function style() {

		echo '<style type="text/css">';
		echo '.woocommerce table.form-table .regular-input.woo_cao-field-days {width:70px}';
		echo '</style>';
	}

}
add_action( 'wp_loaded', array( 'RVOLA\WooCAO', 'load' ), 10 );
register_deactivation_hook( __FILE__, array( 'RVOLA\WooCAO', 'desactivation' ) );
