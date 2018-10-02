<?php
/**
 * Initialize the plugin
 *
 * @package RVOLA\WOO\CAO
 **/

namespace RVOLA\WOO\CAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP
 *
 * @package RVOLA\WOO\CAO
 */
class WP {

	/**
	 * Singleton
	 *
	 * @var singleton.
	 */
	private static $_singleton = null;

	/**
	 * WP constructor.
	 */
	public function __construct() {

		$this->load_languages();
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		$this->required();

		new CAO();
	}

	/**
	 * Required files
	 */
	public function required() {
		require_once dirname( __FILE__ ) . '/class-cao.php';
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
	 * Load language files.
	 */
	public function load_languages() {

		load_plugin_textdomain( 'woo-cancel-abandoned-order', false, plugin_basename( dirname( WOOCAO_FILE ) ) . '/languages' );
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
		if ( plugin_basename( WOOCAO_FILE ) === $plugin_file ) {
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
}
