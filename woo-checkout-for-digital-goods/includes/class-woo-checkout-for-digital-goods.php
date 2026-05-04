<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Checkout_For_Digital_Goods
 * @subpackage Woo_Checkout_For_Digital_Goods/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Checkout_For_Digital_Goods {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Woo_Checkout_For_Digital_Goods_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Public-facing instance (used for deferred quick checkout hook registration).
     *
     * @since 3.8.4
     * @var Woo_Checkout_For_Digital_Goods_Public|null
     */
    protected $plugin_public;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'woo-checkout-for-digital-goods';
        $this->version = WCDG_PLUGIN_VERSION;
        $this->load_dependencies();
        $this->set_locale();
        $this->define_public_hooks();
        $this->define_admin_hooks();
        $prefix = ( is_network_admin() ? 'network_admin_' : '' );
        add_filter(
            "{$prefix}plugin_action_links_" . WCDG_PLUGIN_BASENAME,
            array($this, 'plugin_action_links'),
            10,
            1
        );
        add_filter(
            'plugin_row_meta',
            array($this, 'plugin_row_meta_action_links'),
            20,
            3
        );
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Woo_Checkout_For_Digital_Goods_Loader. Orchestrates the hooks of the plugin.
     * - Woo_Checkout_For_Digital_Goods_i18n. Defines internationalization functionality.
     * - Woo_Checkout_For_Digital_Goods_Admin. Defines all hooks for the admin area.
     * - Woo_Checkout_For_Digital_Goods_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-checkout-for-digital-goods-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-checkout-for-digital-goods-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-checkout-for-digital-goods-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-checkout-for-digital-goods-admin.php';
        $this->loader = new Woo_Checkout_For_Digital_Goods_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Woo_Checkout_For_Digital_Goods_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Woo_Checkout_For_Digital_Goods_i18n();
        $plugin_i18n->set_domain( $this->get_plugin_name() );
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Woo_Checkout_For_Digital_Goods_Public($this->get_plugin_name(), $this->get_version());
        $this->plugin_public = $plugin_public;
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $woo_checkout_status = ( isset( $woo_checkout_unserlize_array['wcdg_status'] ) ? $woo_checkout_unserlize_array['wcdg_status'] : '' );
        $wcdg_user_role_restrict_flag = 1;
        if ( !empty( $woo_checkout_status ) && 1 === $wcdg_user_role_restrict_flag ) {
            $this->loader->add_filter( 'woocommerce_get_country_locale_default', $plugin_public, 'wcdg_prepare_country_locale' );
            $this->loader->add_filter( 'woocommerce_get_country_locale_base', $plugin_public, 'wcdg_prepare_country_locale' );
            $this->loader->add_filter(
                'woocommerce_checkout_fields',
                $plugin_public,
                'wcdg_override_checkout_fields',
                1000
            );
            // Checkout block compatibility code
            $this->loader->add_filter(
                'woocommerce_get_country_locale',
                $plugin_public,
                'wcdg_override_checkout_fields_with_blocks',
                1000
            );
            $this->loader->add_filter( 'woocommerce_localisation_address_formats', $plugin_public, 'wcdg_change_checkout_block_address_format' );
            $this->loader->add_action(
                'woocommerce_blocks_checkout_block_registration',
                $plugin_public,
                'wcdg_update_default_fields_data_with_block',
                10
            );
            $woo_checkout_button_product = ( isset( $woo_checkout_unserlize_array['wcdg_chk_details'] ) ? $woo_checkout_unserlize_array['wcdg_chk_details'] : '' );
            $woo_checkout_button_shop = ( isset( $woo_checkout_unserlize_array['wcdg_chk_prod'] ) ? $woo_checkout_unserlize_array['wcdg_chk_prod'] : '' );
            if ( !empty( $woo_checkout_button_product ) || !empty( $woo_checkout_button_shop ) ) {
                $this->loader->add_action(
                    'init',
                    $this,
                    'wcdg_register_quick_checkout_display_hooks',
                    100
                );
            }
            $this->loader->add_filter(
                'woocommerce_product_single_add_to_cart_text',
                $plugin_public,
                'wcdg_change_add_to_cart_btn_text',
                10,
                2
            );
            $this->loader->add_filter(
                'woocommerce_product_add_to_cart_text',
                $plugin_public,
                'wcdg_change_add_to_cart_btn_text',
                10,
                2
            );
            $this->loader->add_filter(
                'woocommerce_thankyou',
                $plugin_public,
                'wcdg_delay_register_guests',
                10,
                1
            );
        }
        if ( function_exists( 'wcdg_quick_view_feature_available' ) && wcdg_quick_view_feature_available() ) {
            $this->loader->add_action(
                'init',
                $this,
                'wcdg_register_quick_view_display_hooks',
                101
            );
        }
    }

    /**
     * Register quick checkout template hooks on init (late) so filters from the theme
     * or other plugins apply (core loads the plugin on plugins_loaded, before functions.php).
     *
     * @since 3.8.4
     */
    public function wcdg_register_quick_checkout_display_hooks() {
        if ( null === $this->plugin_public ) {
            return;
        }
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $woo_checkout_button_product = ( isset( $woo_checkout_unserlize_array['wcdg_chk_details'] ) ? $woo_checkout_unserlize_array['wcdg_chk_details'] : '' );
        if ( !empty( $woo_checkout_button_product ) ) {
            $quick_checkout_product_hook = apply_filters( 'wcdg_quick_checkout_product_hook', 'woocommerce_after_add_to_cart_button' );
            if ( !is_string( $quick_checkout_product_hook ) || '' === $quick_checkout_product_hook ) {
                $quick_checkout_product_hook = 'woocommerce_after_add_to_cart_button';
            }
            $quick_checkout_product_priority = (int) apply_filters( 'wcdg_quick_checkout_product_priority', 10 );
            add_action( $quick_checkout_product_hook, array($this->plugin_public, 'wcdg_add_quick_checkout_after_add_to_cart_product_page'), $quick_checkout_product_priority );
        }
        $woo_checkout_button_shop = ( isset( $woo_checkout_unserlize_array['wcdg_chk_prod'] ) ? $woo_checkout_unserlize_array['wcdg_chk_prod'] : '' );
        if ( !empty( $woo_checkout_button_shop ) ) {
            $quick_checkout_shop_hook = apply_filters( 'wcdg_quick_checkout_shop_hook', 'woocommerce_after_shop_loop_item' );
            if ( !is_string( $quick_checkout_shop_hook ) || '' === $quick_checkout_shop_hook ) {
                $quick_checkout_shop_hook = 'woocommerce_after_shop_loop_item';
            }
            $quick_checkout_shop_priority = (int) apply_filters( 'wcdg_quick_checkout_shop_priority', 10 );
            add_action( $quick_checkout_shop_hook, array($this->plugin_public, 'wcdg_add_quick_checkout_after_add_to_cart_shop_page'), $quick_checkout_shop_priority );
        }
    }

    /**
     * Register Quick View loop button, modal shell, and Ajax handlers.
     *
     * @since 3.8.5
     */
    public function wcdg_register_quick_view_display_hooks() {
        if ( null === $this->plugin_public || !function_exists( 'wcdg_quick_view_feature_available' ) || !wcdg_quick_view_feature_available() ) {
            return;
        }
        if ( !function_exists( 'wcdg_quick_view_enabled_in_settings' ) || !wcdg_quick_view_enabled_in_settings() ) {
            return;
        }
        add_action( 'wp_ajax_wcdg_quick_view', array($this->plugin_public, 'ajax_quick_view_content') );
        add_action( 'wp_ajax_nopriv_wcdg_quick_view', array($this->plugin_public, 'ajax_quick_view_content') );
        // Register loop + footer on `wp` — is_shop() / is_product_taxonomy() are not reliable on `init`.
        add_action( 'wp', array($this, 'wcdg_register_quick_view_loop_and_footer'), 5 );
    }

    /**
     * After main query: add shop-loop button and modal markup when context matches.
     *
     * @since 3.8.5
     */
    public function wcdg_register_quick_view_loop_and_footer() {
        if ( null === $this->plugin_public || !function_exists( 'wcdg_quick_view_feature_available' ) || !wcdg_quick_view_feature_available() ) {
            return;
        }
        if ( !function_exists( 'wcdg_quick_view_should_load' ) || !wcdg_quick_view_should_load() ) {
            return;
        }
        static $registered = false;
        if ( $registered ) {
            return;
        }
        $registered = true;
        $qv_hook = apply_filters( 'wcdg_quick_view_loop_hook', 'woocommerce_after_shop_loop_item' );
        if ( !is_string( $qv_hook ) || '' === $qv_hook ) {
            $qv_hook = 'woocommerce_after_shop_loop_item';
        }
        $qv_priority = (int) apply_filters( 'wcdg_quick_view_loop_priority', 15 );
        add_action( $qv_hook, array($this->plugin_public, 'wcdg_shop_loop_quick_view_button'), $qv_priority );
        add_action( 'wp_footer', array($this->plugin_public, 'wcdg_quick_view_modal_shell'), 5 );
    }

    private function define_admin_hooks() {
        $get_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $plugin_admin = new Woo_Checkout_For_Digital_Goods_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'activated_plugin', $plugin_admin, 'wcdg_welcome_screen_do_activation_redirect' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'wcdg_checkout_for_digital_create_page' );
        $this->loader->add_action(
            'admin_head',
            $plugin_admin,
            'wcdg_remove_admin_submenus',
            999
        );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'wcdg_admin_menu_icon_style' );
        $this->loader->add_action( 'wp_ajax_wcdg_plugin_setup_wizard_submit', $plugin_admin, 'wcdg_plugin_setup_wizard_submit' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'wcdg_send_wizard_data_after_plugin_activation' );
        if ( !empty( $get_page ) && false !== strpos( $get_page, 'wcdg' ) ) {
            // Plugin footer review
            $this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'wcdg_admin_footer_review' );
        }
    }

    /**
     * Return the plugin action links.  This will only be called if the plugin
     * is active.
     *
     * @param array $actions associative array of action names to anchor tags
     *
     * @return array associative array of plugin action links
     * @since 1.0.0
     */
    public function plugin_action_links( $actions ) {
        $custom_actions = array(
            'configure' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array(
                'page' => 'wcdg-general-setting',
            ), admin_url( 'admin.php' ) ) ), __( 'Settings', 'woo-checkout-for-digital-goods' ) ),
            'docs'      => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'https://docs.thedotstore.com/collection/165-digital-goods-for-checkout' ), __( 'Docs', 'woo-checkout-for-digital-goods' ) ),
            'support'   => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'www.thedotstore.com/support' ), __( 'Support', 'woo-checkout-for-digital-goods' ) ),
        );
        // add the links to the front of the actions list
        return array_merge( $custom_actions, $actions );
    }

    /**
     * Add review stars in plugin row meta
     *
     * @since 1.0.0
     */
    public function plugin_row_meta_action_links( $plugin_meta, $plugin_file, $plugin_data ) {
        if ( isset( $plugin_data['TextDomain'] ) && $plugin_data['TextDomain'] !== 'woo-checkout-for-digital-goods' ) {
            return $plugin_meta;
        }
        $url = '';
        $url = esc_url( 'https://wordpress.org/plugins/woo-checkout-for-digital-goods/#reviews' );
        $plugin_meta[] = sprintf( '<a href="%s" target="_blank" style="color:#f5bb00;">%s</a>', $url, esc_html( '★★★★★' ) );
        return $plugin_meta;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Woo_Checkout_For_Digital_Goods_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
