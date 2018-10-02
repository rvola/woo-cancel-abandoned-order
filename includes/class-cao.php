<?php
/**
 * Main class of the plugin
 *
 * @package RVOLA\WOO\CAO
 **/

namespace RVOLA\WOO\CAO;

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
				strtotime( 'yesterday 0 hours' ), 'daily', self::CRON_EVENT
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
					&& isset( $options['woocao_days'] )
					&& ! empty( $options['woocao_days'] )
				) {
					$restock = isset( $options['woocao_restock'] ) && 'yes' === $options['woocao_restock'] ? 'yes' : 'no';

					$old_date        = strtotime( 'today -' . $options['woocao_days'] . ' days' );
					$old_date_format = date( 'Y-m-d 00:00:00', $old_date );

					$orders = $wpdb->get_results(
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
					if ( $orders ) {
						foreach ( $orders as $order ) {
							// Cancel order.
							$this->cancel_order( $order->ID );

							// Restock product.
							if ( 'yes' === $restock ) {
								$this->restock( $order->ID );
							}
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
		$order = new \WC_Order( $order_id );

		$order->update_status(
			'cancelled',
			__( 'Cancellation of the order because payment not received at time.', 'woo-cancel-abandoned-order' )
		);
		do_action( 'woo_cao_cancel_order', $order_id );

	}

	/**
	 * Will check and store the products of the canceled order.
	 *
	 * @param int $order_id order ID.
	 */
	private function restock( $order_id ) {

		$order = new \WC_Order( $order_id );

		$line_items = $order->get_items();

		foreach ( $line_items as $item_id => $item ) {

			$item_data = $item->get_data();
			$product   = $item->get_product();

			if ( $product && $product->managing_stock() ) {
				$old_stock = $product->get_stock_quantity();
				$new_stock = wc_update_product_stock( $product, $item_data['quantity'], 'increase' );

				// translators: %1$s is name of product, %2$s is initial stock, %3$s is new stock after cancel order.
				$order->add_order_note( sprintf( _x( '%1$s stock increased from %2$s to %3$s.', '%1$s is name of product, %2$s is initial stock, %3$s is new stock after cancel order', 'woo-cancel-abandoned-order' ), $product->get_name(), $old_stock, $new_stock ) );
				do_action( 'woo_cao_restock_item', $product->get_id(), $old_stock, $new_stock, $order, $product );
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
				'default'     => apply_filters( 'woo_cao_default_days', '15' ),
				'placeholder' => __( 'days', 'woo-cancel-abandoned-order' ),
				'class'       => 'woo_cao-field-days',
			),
		);

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$new_fields['woocao_restock'] = array(
				'title'       => __( 'Stock', 'woo-cancel-abandoned-order' ),
				'type'        => 'checkbox',
				'label'       => __( 'Restock the products of abandoned orders.', 'woo-cancel-abandoned-order' ),
				'default'     => 'no',
				'description' => __( 'If enabled, each product contained in orders canceled by the system, will be restocked in your products.', 'woo-cancel-abandoned-order' ),
			);
		}

		return array_merge( $fields, $new_fields );

	}

	/**
	 * Load assets CSS & JS
	 */
	public function assets( $hook ) {
		if ( 'woocommerce_page_wc-settings' == $hook ) {
			wp_enqueue_style( 'woo_cao', plugins_url( 'assets/woo_cao.css', WOOCAO_FILE ), null, WOOCAO_VERSION, 'all' );
		}
	}
}
