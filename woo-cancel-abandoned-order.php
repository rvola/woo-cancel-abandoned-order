<?php
/*
Plugin Name:		    WooCommerce Cancel Abandoned Order
Plugin URI:			    https://github.com/rvola/woo-cancel-abandoned-order

Description:		    Cancel "on hold" orders after a certain number of days or by hours

Version:			    1.8.0
Revision:			    2020-04-11
Creation:               2017-10-28

Author:				    studio RVOLA
Author URI:			    https://www.rvola.com

Text Domain:		    woo-cancel-abandoned-order
Domain Path:		    /languages

Requires at least:      4.0
Tested up to:           5.4
Requires PHP:           7.0
WC requires at least:   2.2
WC tested up to:        4.0

License:                GNU General Public License v3.0
License URI:            https://www.gnu.org/licenses/gpl-3.0.html
*/

namespace RVOLA\WOO\CAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOCAO_FILE', __FILE__ );
define( 'WOOCAO_VERSION', '1.8.0' );

include_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

	require_once dirname( WOOCAO_FILE ) . '/includes/class-wp.php';
	add_action( 'wp_loaded', array( __NAMESPACE__ . '\\WP', 'instance' ), 10 );

	register_deactivation_hook( WOOCAO_FILE, array( __NAMESPACE__ . '\\CAO', 'clean_cron' ) );
}

