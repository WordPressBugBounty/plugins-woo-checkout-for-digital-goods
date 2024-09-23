<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Checkout_For_Digital_Goods
 * @subpackage Woo_Checkout_For_Digital_Goods/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Checkout_For_Digital_Goods_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		if ( is_multisite() ) {
            $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
            $active_plugins = array_unique( $active_plugins );
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
            	wp_die( "<strong>Digital Goods for WooCommerce Checkout</strong> plugin requires <strong>WooCommerce</strong>. Return to <a href='" . esc_url( get_admin_url( null, 'plugins.php' ) ) . "'>Plugins page</a>." );
            } else {
            	set_transient( '_welcome_screen_wcdg_mode_activation_redirect_data', true, 30 );
            }
        } else {
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                wp_die( "<strong>Digital Goods for WooCommerce Checkout</strong> plugin requires <strong>WooCommerce</strong>. Return to <a href='" . esc_url( get_admin_url( null, 'plugins.php' ) ) . "'>Plugins page</a>." );
            } else {
                set_transient( '_welcome_screen_wcdg_mode_activation_redirect_data', true, 30 );
            }
        }
	}

}
