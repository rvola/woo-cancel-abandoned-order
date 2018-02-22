<?php
/*
Plugin Name:		    WooCommerce Cancel Abandoned Order
Plugin URI:			    https://github.com/rvola/woo-cancel-abandoned-order

Description:		    Cancel "on hold" orders after a certain number of days

Version:			    1.2.0
Revision:			    2018-02-22
Creation:               2017-10-28

Author:				    studio RVOLA
Author URI:			    https://www.rvola.com

Text Domain:		    woo-cancel-abandoned-order
Domain Path:		    /languages

Requires at least:      4.0
Tested up to:           4.9
Requires PHP:           5.3
WC requires at least:   2.2.0
WC tested up to:        3.3.0

License:                GNU General Public License v3.0
License URI:            https://www.gnu.org/licenses/gpl-3.0.html
*/

namespace RVOLA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/includes/class-woocao.php';

add_action( 'wp_loaded', array( 'RVOLA\WooCAO', 'load' ), 10 );
register_deactivation_hook( __FILE__, array( 'RVOLA\WooCAO', 'desactivation' ) );
