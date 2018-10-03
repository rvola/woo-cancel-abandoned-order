<?php
/**
 * Update function
 *
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1.4.1
 * Clean cron. / Reload for olfder PHP
 */
function woo_cao_update__141() {
	if ( version_compare( phpversion(), '7.0', '<' ) ) {
		woo_cao_update__140();
	}
}

/**
 * 1.4.0
 * Clean cron.
 */
function woo_cao_update__140() {
	wp_clear_scheduled_hook( 'woo_cao_cron' );
}

