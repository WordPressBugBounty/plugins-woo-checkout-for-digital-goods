<?php

/**
 * Plugin Name: Digital Goods for WooCommerce Checkout
 * Plugin URI:        https://www.thedotstore.com/woocommerce-checkout-for-digital-goods/
 * Description:       This plugin will remove billing address fields for downloadable and virtual products.
 * Version:           3.8.0
 * Author:            theDotstore
 * Author URI:        https://www.thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-checkout-for-digital-goods
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 * 
 * WC requires at least:4.5
 * WP tested up to:     6.6.1
 * WC tested up to:     9.2.3
 * Requires PHP:        7.2
 * Requires at least:   5.0
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die;
}
if ( function_exists( 'wcfdg_fs' ) ) {
    wcfdg_fs()->set_basename( false, __FILE__ );
    return;
}
if ( !function_exists( 'wcfdg_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wcfdg_fs() {
        global $wcfdg_fs;
        if ( !isset( $wcfdg_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wcfdg_fs = fs_dynamic_init( array(
                'id'             => '4703',
                'slug'           => 'woo-checkout-for-digital-goods',
                'type'           => 'plugin',
                'public_key'     => 'pk_2eb1a2c306bc0ab838b9439f8fa73',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 14,
                    'is_require_payment' => true,
                ),
                'menu'           => array(
                    'slug'       => 'wcdg-general-setting',
                    'first-path' => 'admin.php?page=wcdg-general-setting',
                    'support'    => false,
                    'contact'    => false,
                ),
                'is_live'        => true,
            ) );
        }
        return $wcfdg_fs;
    }

    // Init Freemius.
    wcfdg_fs();
    // Signal that SDK was initiated.
    do_action( 'wcfdg_fs_loaded' );
    wcfdg_fs()->get_upgrade_url();
}
if ( !defined( 'WCDG_PLUGIN_NAME' ) ) {
    define( 'WCDG_PLUGIN_NAME', 'Digital Goods for WooCommerce Checkout' );
}
if ( !defined( 'WCDG_PLUGIN_URL' ) ) {
    define( 'WCDG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'WCDG_PLUGIN_PATH' ) ) {
    define( 'WCDG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'WCDG_PLUGIN_BASENAME' ) ) {
    define( 'WCDG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( !defined( 'WCDG_PLUGIN_VERSION' ) ) {
    define( 'WCDG_PLUGIN_VERSION', '3.8.0' );
}
if ( !defined( 'WCDG_SLUG' ) ) {
    define( 'WCDG_SLUG', 'woo-checkout-for-digital-goods' );
}
if ( !defined( 'WCDG_STORE_URL' ) ) {
    define( 'WCDG_STORE_URL', 'https://www.thedotstore.com/' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-checkout-for-digital-goods-activator.php
 */
if ( !function_exists( 'activate_woo_checkout_for_digital_goods' ) ) {
    function activate_woo_checkout_for_digital_goods() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-checkout-for-digital-goods-activator.php';
        Woo_Checkout_For_Digital_Goods_Activator::activate();
    }

}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-checkout-for-digital-goods-deactivator.php
 */
if ( !function_exists( 'deactivate_woo_checkout_for_digital_goods' ) ) {
    function deactivate_woo_checkout_for_digital_goods() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-checkout-for-digital-goods-deactivator.php';
        Woo_Checkout_For_Digital_Goods_Deactivator::deactivate();
    }

}
register_activation_hook( __FILE__, 'activate_woo_checkout_for_digital_goods' );
register_deactivation_hook( __FILE__, 'deactivate_woo_checkout_for_digital_goods' );
add_action( 'admin_init', 'wcdg_deactivate_plugin' );
if ( !function_exists( 'wcdg_deactivate_plugin' ) ) {
    function wcdg_deactivate_plugin() {
        $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        if ( is_multisite() ) {
            $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
            $active_plugins = array_unique( $active_plugins );
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                if ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) {
                    deactivate_plugins( '/woo-checkout-for-digital-goods-premium/woo-checkout-for-digital-goods.php', true );
                } else {
                    deactivate_plugins( '/woo-checkout-for-digital-goods/woo-checkout-for-digital-goods.php', true );
                }
            }
        } else {
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                if ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) {
                    deactivate_plugins( '/woo-checkout-for-digital-goods-premium/woo-checkout-for-digital-goods.php', true );
                } else {
                    deactivate_plugins( '/woo-checkout-for-digital-goods/woo-checkout-for-digital-goods.php', true );
                }
            }
        }
    }

}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-checkout-for-digital-goods.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if ( !function_exists( 'run_woo_checkout_for_digital_goods' ) ) {
    function run_woo_checkout_for_digital_goods() {
        $plugin = new Woo_Checkout_For_Digital_Goods();
        $plugin->run();
    }

}
/**
 * Hide freemius account tab
 *
 * @since 3.7.2
 */
if ( !function_exists( 'wcdg_hide_account_tab' ) ) {
    function wcdg_hide_account_tab() {
        return true;
    }

    wcfdg_fs()->add_filter( 'hide_account_tabs', 'wcdg_hide_account_tab' );
}
/**
 * Include plugin header on freemius account page
 *
 * @since 3.7.2
 */
if ( !function_exists( 'wcdg_load_plugin_header_after_account' ) ) {
    function wcdg_load_plugin_header_after_account() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-header.php';
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-footer.php';
    }

    wcfdg_fs()->add_action( 'after_account_details', 'wcdg_load_plugin_header_after_account' );
}
/**
 * Hide billing and payments details from freemius account page
 *
 * @since 3.7.2
 */
if ( !function_exists( 'wcdg_hide_billing_and_payments_info' ) ) {
    function wcdg_hide_billing_and_payments_info() {
        return true;
    }

    wcfdg_fs()->add_action( 'hide_billing_and_payments_info', 'wcdg_hide_billing_and_payments_info' );
}
/**
 * Hide powerd by popup from freemius account page
 *
 * @since 3.7.2
 */
if ( !function_exists( 'wcdg_hide_freemius_powered_by' ) ) {
    function wcdg_hide_freemius_powered_by() {
        return true;
    }

    wcfdg_fs()->add_action( 'hide_freemius_powered_by', 'wcdg_hide_freemius_powered_by' );
}
/**
 * Start plugin setup wizard before license activation screen
 *
 * @since 3.7.2
 */
if ( !function_exists( 'wcdg_load_plugin_setup_wizard_connect_before' ) ) {
    function wcdg_load_plugin_setup_wizard_connect_before() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/dots-plugin-setup-wizard.php';
        ?>
        <div class="tab-panel" id="step5">
            <div class="ds-wizard-wrap">
                <div class="ds-wizard-content">
                    <h2 class="cta-title"><?php 
        echo esc_html__( 'Activate Plugin', 'woo-checkout-for-digital-goods' );
        ?></h2>
                </div>
        <?php 
    }

    wcfdg_fs()->add_action( 'connect/before', 'wcdg_load_plugin_setup_wizard_connect_before' );
}
/**
 * End plugin setup wizard after license activation screen
 *
 * @since 3.7.2
 */
if ( !function_exists( 'wcdg_load_plugin_setup_wizard_connect_after' ) ) {
    function wcdg_load_plugin_setup_wizard_connect_after() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-footer.php';
    }

    wcfdg_fs()->add_action( 'connect/after', 'wcdg_load_plugin_setup_wizard_connect_after' );
}
add_action( 'plugins_loaded', 'wcdg_initialize_plugin' );
/**
 * Check Initialize plugin in case of WooCommerce plugin is missing.
 *
 * @since    1.0.0
 */
if ( !function_exists( 'wcdg_initialize_plugin' ) ) {
    function wcdg_initialize_plugin() {
        $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        if ( is_multisite() ) {
            $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
            $active_plugins = array_unique( $active_plugins );
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                add_action( 'admin_notices', 'wcdg_plugin_admin_notice' );
            } else {
                run_woo_checkout_for_digital_goods();
            }
        } else {
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                add_action( 'admin_notices', 'wcdg_plugin_admin_notice' );
            } else {
                run_woo_checkout_for_digital_goods();
            }
        }
        load_plugin_textdomain( 'woo-checkout-for-digital-goods', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

}
/**
 * Order Thank you page form
 *
 * @since    1.0.0
 */
if ( !function_exists( 'wcdg_thankyou_page_form' ) ) {
    add_action( 'woocommerce_thankyou', 'wcdg_thankyou_page_form', 12 );
    function wcdg_thankyou_page_form() {
        if ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) {
            // if guest checkout enabled
            $wegc = get_option( 'woocommerce_enable_guest_checkout' );
            if ( 'yes' !== $wegc || is_user_logged_in() ) {
                $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
                $wcdg_ty_address_1_field_display = ( isset( $woo_checkout_unserlize_array['wcdg_allow_additional_field_update_flag'] ) ? $woo_checkout_unserlize_array['wcdg_allow_additional_field_update_flag'] : '' );
                $endpoint = apply_filters( 'endpoing_edit_address', 'edit-address/billing/' );
                $edit_profile = wc_get_account_endpoint_url( $endpoint );
                $billing_msg_title = apply_filters( 'default_billing_msg_title', __( 'Want to update the billing information?', 'woo-checkout-for-digital-goods' ) );
                if ( !empty( $wcdg_ty_address_1_field_display ) ) {
                    ?>
                    <div class="quick_edit_container">
                        <h2><?php 
                    esc_html_e( $billing_msg_title, 'woo-checkout-for-digital-goods' );
                    ?></h2>
                        <?php 
                    echo '<a href="' . esc_url( $edit_profile ) . '" class="button wcdg_delay_account">' . esc_html( "Update now", "woo-checkout-for-digital-goods" ) . '</a>';
                    ?>
                    </div>
                    <?php 
                }
            }
        }
    }

}
/**
 * Show admin notice in case of WooCommerce plugin is missing.
 *
 * @since    1.0.0
 */
if ( !function_exists( 'wcdg_plugin_admin_notice' ) ) {
    function wcdg_plugin_admin_notice() {
        $vpe_plugin = esc_html__( 'Digital Goods for WooCommerce Checkout', 'woo-checkout-for-digital-goods' );
        $wc_plugin = esc_html__( 'WooCommerce', 'woo-checkout-for-digital-goods' );
        ?>
        <div class="error">
            <p>
                <?php 
        echo sprintf( esc_html__( '%1$s requires %2$s to be installed & activated!', 'woo-checkout-for-digital-goods' ), '<strong>' . esc_html( $vpe_plugin ) . '</strong>', '<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '" target="_blank"><strong>' . esc_html( $wc_plugin ) . '</strong></a>' );
        ?>
            </p>
        </div>
        <?php 
    }

}
/**
 * Plugin compability with WooCommerce HPOS
 *
 * @since 3.7.2
 */
add_action( 'before_woocommerce_init', function () {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );