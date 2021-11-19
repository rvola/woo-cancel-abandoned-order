<?php

namespace RVOLA\WOO\CAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Stripe
 *
 * @package RVOLA\WOO\CAO
 */
class Stripe {

	/**
	 * Slug page options
	 */
	const SLUG = 'woocao';

	/**
	 * Stripe constructor
	 */
	public function __construct() {
		add_action( 'wc_stripe_gateway_admin_options_wrapper', array( $this, 'messageNewStripe' ), 10 );
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'addTab' ), 50, 1 );
		add_action( 'woocommerce_settings_tabs_' . self::SLUG, array( $this, 'display' ), 10 );
		add_action( 'woocommerce_update_options_' . self::SLUG, array( $this, 'save' ), 10 );
	}

	/**
	 * Display a message if the user is using Stripe
	 */
	public function messageNewStripe() {
		printf( '<h2>%s</h2>', esc_html__( 'WooCommerce Cancel Abandoned Order', 'woo-cancel-abandoned-order' ) );
		printf(
			__( 'We have moved the settings from WOOCAO to Stripe %s', 'woo-cancel-abandoned-order' ),
			sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php' ) . '?page=wc-settings&tab=' . self::SLUG,
				__( 'here', 'woo-cancel-abandoned-order' )
			)
		);
	}

	/**
	 * Add WOOCAO in WooCommerce options
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function addTab( $tabs ) {
		$tabs[ self::SLUG ] = __( 'WOOCAO', 'woo-cancel-abandoned-order' );

		return $tabs;
	}

	/**
	 *Displays the settings fields
	 */
	public function display() {
		woocommerce_admin_fields( $this->fields() );
	}

	/**
	 *Save fields
	 */
	public function save() {
		woocommerce_update_options( $this->fields() );
	}

	/**
	 * WOOCAO fields for Stripe
	 * @return array
	 */
	private function fields() {

		$stripe_base = 'woocommerce_stripe_settings[%s]';

		return array(
			'woocao_stripe'  => array(
				'name'    => __( 'Stripe' ),
				'type'    => 'title',
				'desc'    => '',
				'default' => '',
			),
			'woocao_enabled' => array(
				'title'   => esc_html__( 'Enable/Disable', 'woo-cancel-abandoned-order' ),
				'type'    => 'checkbox',
				'desc'    => esc_html__( 'Enable this option to automatically cancel all "on Hold" orders that you have not received payment for.', 'woo-cancel-abandoned-order' ),
				'default' => 'no',
				'id'      => sprintf( $stripe_base, 'woocao_enabled' ),
			),
			'woocao_mode'    => array(
				'title'   => esc_html__( 'Mode', 'woo-cancel-abandoned-order' ),
				'type'    => 'select',
				'default' => 'daily',
				'options' => array(
					'hourly' => esc_html__( 'Hourly', 'woo-cancel-abandoned-order' ),
					'daily'  => esc_html__( 'Daily', 'woo-cancel-abandoned-order' )
				),
				'class'   => 'woo_cao-field-mode',
				'id'      => sprintf( $stripe_base, 'woocao_mode' ),
			),
			'woocao_hours'   => array(
				'title'   => esc_html__( 'Lifetime in hour', 'woo-cancel-abandoned-order' ),
				'type'    => 'number',
				'desc'    => esc_html__( 'Enter the number of hours (whole number) during which the system must consider a "pending" command as canceled.', 'woo-cancel-abandoned-order' ),
				'default' => apply_filters( 'woo_cao_default_hours', '1' ),
				'class'   => 'woo_cao-field-hourly woo_cao-field-moded',
				'id'      => sprintf( $stripe_base, 'woocao_hours' ),
			),
			'woocao_days'    => array(
				'title'   => esc_html__( 'Lifetime in days', 'woo-cancel-abandoned-order' ),
				'type'    => 'number',
				'desc'    => esc_html__( 'Enter the number of days that the system must consider a "on Hold" order as canceled.', 'woo-cancel-abandoned-order' ),
				'default' => apply_filters( 'woo_cao_default_days', '15' ),
				'class'   => 'woo_cao-field-daily woo_cao-field-moded',
				'id'      => sprintf( $stripe_base, 'woocao_days' ),
			),

			'sectionend' => array(
				'type' => 'sectionend'
			),
		);
	}

}
