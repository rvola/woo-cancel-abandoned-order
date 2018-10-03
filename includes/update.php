<?php
/**
 * Update function
 *
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1.4.0
 * Clean cron.
 */
function woo_cao_update__140() {
	wp_clear_scheduled_hook( 'woo_cao_cron' );
}

