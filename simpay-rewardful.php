<?php
/**
 * Plugin Name: WP Simple Pay Pro 3 Rewardful add-on
 */

/**
 * TODO Plugin polish...
 * Retrieve API key from constant in wp-config.php for now, and later as saved plugin setting?
 * Define other constants (plugin version, etc)
 * Add check for WP Simple Pay Pro core plugin.
 * Add activation/deactivation handling
 * Add translation capabilities (use load_textdomain)
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO Retrieve Rewardful API key from constant in wp-config.php.

if ( ! defined( 'REWARDFUL_API_KEY' ) ) {
	define( 'REWARDFUL_API_KEY', 'REWARDFUL-API-KEY' );
}


// Don't allow multiple versions to be active.
if ( ! class_exists( 'Simpay_Rewardful' ) ) {

	final class Simpay_Rewardful {

		private static $instance;

		/**
		 * Main Simpay_Rewardful instance
		 *
		 * Insures that only one instance of WPForms exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();

				add_action( 'wp_enqueue_scripts', array( self::$instance, 'enqueue_main_script' ) );
				add_filter( 'script_loader_tag', array( self::$instance, 'replace_script_tag' ), 10, 2 );

				add_filter( 'simpay_more_form_attributes', array( self::$instance, 'add_form_tag_data_attr' ) );
			}

			return self::$instance;
		}

		public function enqueue_main_script() {
			wp_enqueue_script( 'rewardful', 'https://r.wdfl.co/rw.js', array(), null, true );
		}

		/**
		 * Rewrite script tag to include async & Rewardful API key in a data attribute.
		 *
		 * Taken from https://github.com/Automattic/jetpack/blob/master/modules/widgets/googleplus-badge.php
		 */
		public function replace_script_tag( $tag, $handle ) {
			if ( 'rewardful' !== $handle ) {
				return $tag;
			}

			return str_replace( ' src', " async data-rewardful='" . esc_attr( REWARDFUL_API_KEY ) . "' src", $tag );
		}

		/**
		 * Add Rewardful data attribute to payment form tag.
		 */
		public function add_form_tag_data_attr() {

			return 'data-rewardful';
		}
	}

	/**
	 * The function which returns the one Simpay_Rewardful instance.
	 */
	function Simpay_Rewardful() {
		return Simpay_Rewardful::instance();
	}

	Simpay_Rewardful();

} // end if()
