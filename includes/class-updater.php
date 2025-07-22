<?php
/**
 * Updater
 *
 * @package RVOLA\WOO\CAO
 **/

namespace RVOLA\WOO\CAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Updater
 *
 * @package RVOLA\WOO\CAO
 */
final class Updater {

	/**
	 * Name of the option name.
	 */
	const OPTION_NAME = 'woo_cao_version';

	/**
	 * Updater constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'check_update' ) );
	}

	/**
	 * Check if the current version differs from the one previously installed.
	 */
	public function check_update() {
		if ( WOOCAO_VERSION != get_option( self::OPTION_NAME ) ) {
			$this->update();
		}
	}

	/**
	 * Execute the update function if it exists.
	 */
	private function update() {
		$func = $this->name_update();
		if ( function_exists( $func ) ) {
			$func();
		}
		update_option( self::OPTION_NAME, WOOCAO_VERSION, true );
	}

	/**
	 * Returns the name of the function to update..
	 *
	 * @return string
	 */
	private function name_update() {
		return sprintf(
			'woo_cao_update__%d',
			str_replace( '.', '', WOOCAO_VERSION )
		);
	}
}
