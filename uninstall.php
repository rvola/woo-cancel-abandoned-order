<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'woo_cao_version' );

if ( function_exists( 'as_unschedule_action' ) ) {
	as_unschedule_action( 'woo_cao_cron' );
}
wp_clear_scheduled_hook( 'woo_cao_cron' );
