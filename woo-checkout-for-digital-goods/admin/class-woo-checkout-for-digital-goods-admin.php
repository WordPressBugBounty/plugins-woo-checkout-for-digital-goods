<?php

//phpcs:ignore
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woo_Checkout_For_Digital_Goods
 * @subpackage Woo_Checkout_For_Digital_Goods/public
 */
class Woo_Checkout_For_Digital_Goods_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     *
     * @param string $hook display current page name
     */
    public function enqueue_styles( $hook ) {
        if ( false !== strpos( $hook, '_page_wcdg' ) ) {
            wp_enqueue_style( 'woocommerce_admin_styles' );
            wp_enqueue_style(
                $this->plugin_name . '-select2-style',
                plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-header-style',
                plugin_dir_url( __FILE__ ) . 'css/plugin-header.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'css/woo-checkout-for-digital-goods-admin.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'plugin-setup-wizard',
                plugin_dir_url( __FILE__ ) . 'css/plugin-setup-wizard.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'plugin-upgrade-dashboard',
                plugin_dir_url( __FILE__ ) . 'css/upgrade-dashboard.css',
                array(),
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     *
     * @param string $hook display current page name
     */
    public function enqueue_scripts( $hook ) {
        // Always enqueue jQuery UI Sortable for the general settings page (drag-and-drop billing fields)
        if ( false !== strpos( $hook, '_page_wcdg' ) ) {
            wp_enqueue_script(
                $this->plugin_name . '-help-scout-beacon-js',
                plugin_dir_url( __FILE__ ) . 'js/help-scout-beacon.js',
                array('jquery'),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . 'freemius_pro',
                'https://checkout.freemius.com/checkout.min.js',
                array('jquery'),
                $this->version,
                true
            );
            wp_enqueue_script(
                $this->plugin_name . 'wcdg-admin-default-js',
                plugin_dir_url( __FILE__ ) . 'js/woo-checkout-for-digital-goods-admin.js',
                array('jquery', 'jquery-ui-dialog'),
                $this->version,
                false
            );
            wp_localize_script( $this->plugin_name . 'wcdg-admin-default-js', 'admin_basic_vars', array(
                'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
                'dpb_api_url'             => WCDG_STORE_URL,
                'setup_wizard_ajax_nonce' => wp_create_nonce( 'wizard_ajax_nonce' ),
            ) );
            wp_enqueue_script( 'wp-pointer' );
            wp_enqueue_script( 'jquery-tiptip' );
            wp_enqueue_script( 'jquery-blockui' );
            // Always enqueue jQuery UI Sortable for drag-and-drop
            wp_enqueue_script( 'jquery-ui-sortable' );
        }
    }

    /*
     * Digital Checkout Menu
     *
     * @since 1.0.0
     */
    public function wcdg_checkout_for_digital_create_page() {
        global $GLOBALS;
        if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
            add_menu_page(
                'Dotstore Plugins',
                __( 'Dotstore Plugins', 'woo-checkout-for-digital-goods' ),
                'null',
                'dots_store',
                '',
                'dashicons-marker',
                25
            );
        }
        add_submenu_page(
            'dots_store',
            'Digital Goods For Checkout',
            'Digital Goods For Checkout',
            'manage_options',
            'wcdg-general-setting',
            array($this, 'wcdg_general_setting_page')
        );
        add_submenu_page(
            'dots_store',
            'Quick Checkout',
            'Quick Checkout',
            'manage_options',
            'wcdg-quick-checkout',
            array($this, 'wcdg_quick_checkout_page')
        );
        add_submenu_page(
            'dots_store',
            'Master Settings',
            'Master Settings',
            'manage_options',
            'wcdg-master-settings',
            array($this, 'wcdg_master_settings_page')
        );
        add_submenu_page(
            'dots_store',
            'Import / Export',
            'Import / Export',
            'manage_options',
            'wcdg-import-export',
            array($this, 'wcdg_import_export_page')
        );
        add_submenu_page(
            'dots_store',
            'Getting Started',
            'Getting Started',
            'manage_options',
            'wcdg-get-started',
            array($this, 'wcdg_get_started_page')
        );
        add_submenu_page(
            'dots_store',
            'Get Premium',
            'Get Premium',
            'manage_options',
            'wcdg-upgrade-dashboard',
            array($this, 'wcdg_free_user_upgrade_page')
        );
    }

    /**
     * Add custom css for dotstore icon in admin area
     *
     * @since 3.7.2
     *
     */
    public function wcdg_admin_menu_icon_style() {
        echo '<style>
          .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
          li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
          @media only screen and (max-width: 960px){
              .toplevel_page_dots_store .dashicons-marker::after{left:14px;}
          } </style>';
    }

    /**
     * General Setting Page
     *
     * @since    1.0.0
     */
    public function wcdg_general_setting_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdg-general-setting.php';
    }

    /**
     * Quick Checkout Page
     *
     * @since    1.0.0
     */
    public function wcdg_quick_checkout_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdg-quick-checkout.php';
    }

    /**
     * Quick Checkout Page
     *
     * @since    3.7.0
     */
    public function wcdg_import_export_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdg-import-export.php';
    }

    /**
     * Quick guide page
     *
     * @since    1.0.0
     */
    public function wcdg_get_started_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdg-get-started-page.php';
    }

    /**
     * Get premium page
     *
     * @since    1.0.0
     */
    public function wcdg_free_user_upgrade_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/dots-upgrade-dashboard.php';
    }

    /**
     * Remove the Extra flate rate menu in dashboard
     *
     */
    public function wcdg_remove_admin_submenus() {
        remove_submenu_page( 'dots_store', 'dots_store' );
        remove_submenu_page( 'dots_store', 'wcdg-quick-checkout' );
        remove_submenu_page( 'dots_store', 'wcdg-get-started' );
        remove_submenu_page( 'dots_store', 'wcdg-import-export' );
        remove_submenu_page( 'dots_store', 'wcdg-master-settings' );
        remove_submenu_page( 'dots_store', 'wcdg-upgrade-dashboard' );
    }

    /**
     * Admin footer review
     *
     * @since 1.0.0
     */
    public function wcdg_admin_footer_review() {
        $url = '';
        $url = esc_url( 'https://wordpress.org/plugins/woo-checkout-for-digital-goods/#reviews' );
        $html = sprintf( wp_kses( __( '<strong>We need your support</strong> to keep updating and improving the plugin. Please <a href="%1$s" target="_blank">help us by leaving a good review</a> :) Thanks!', 'woo-checkout-for-digital-goods' ), array(
            'strong' => array(),
            'a'      => array(
                'href'   => array(),
                'target' => 'blank',
            ),
        ) ), esc_url( $url ) );
        echo wp_kses_post( $html );
    }

    /**
     * Redirect to quick start guide after plugin activation
     *
     * @since    1.0.0
     */
    public function wcdg_welcome_screen_do_activation_redirect() {
        // if no activation redirect
        if ( !get_transient( '_welcome_screen_wcdg_mode_activation_redirect_data' ) ) {
            return;
        }
        // Delete the redirect transient
        delete_transient( '_welcome_screen_wcdg_mode_activation_redirect_data' );
        // if activating from network, or bulk
        $activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( is_network_admin() || isset( $activate_multi ) ) {
            return;
        }
        // Redirect to extra cost welcome  page
        wp_safe_redirect( add_query_arg( array(
            'page' => 'wcdg-get-started',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Get dynamic promotional bar of plugin
     *
     * @param   String  $plugin_slug  slug of the plugin added in the site option
     * @since   3.7.2
     * 
     * @return  null
     */
    public function wcdg_get_promotional_bar( $plugin_slug = '' ) {
        $promotional_bar_upi_url = WCDG_STORE_URL . 'wp-json/dpb-promotional-banner/v2/dpb-promotional-banner?' . wp_rand();
        $promotional_banner_request = wp_remote_get( $promotional_bar_upi_url );
        //phpcs:ignore
        if ( empty( $promotional_banner_request->errors ) ) {
            $promotional_banner_request_body = $promotional_banner_request['body'];
            $promotional_banner_request_body = json_decode( $promotional_banner_request_body, true );
            echo '<div class="dynamicbar_wrapper">';
            if ( !empty( $promotional_banner_request_body ) && is_array( $promotional_banner_request_body ) ) {
                foreach ( $promotional_banner_request_body as $promotional_banner_request_body_data ) {
                    $promotional_banner_id = $promotional_banner_request_body_data['promotional_banner_id'];
                    $promotional_banner_cookie = $promotional_banner_request_body_data['promotional_banner_cookie'];
                    $promotional_banner_image = $promotional_banner_request_body_data['promotional_banner_image'];
                    $promotional_banner_description = $promotional_banner_request_body_data['promotional_banner_description'];
                    $promotional_banner_button_group = $promotional_banner_request_body_data['promotional_banner_button_group'];
                    $dpb_schedule_campaign_type = $promotional_banner_request_body_data['dpb_schedule_campaign_type'];
                    $promotional_banner_target_audience = $promotional_banner_request_body_data['promotional_banner_target_audience'];
                    if ( !empty( $promotional_banner_target_audience ) ) {
                        $plugin_keys = array();
                        if ( is_array( $promotional_banner_target_audience ) ) {
                            foreach ( $promotional_banner_target_audience as $list ) {
                                $plugin_keys[] = $list['value'];
                            }
                        } else {
                            $plugin_keys[] = $promotional_banner_target_audience['value'];
                        }
                        $display_banner_flag = false;
                        if ( in_array( 'all_customers', $plugin_keys, true ) || in_array( $plugin_slug, $plugin_keys, true ) ) {
                            $display_banner_flag = true;
                        }
                    }
                    if ( true === $display_banner_flag ) {
                        if ( 'default' === $dpb_schedule_campaign_type ) {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes', time() + 86400 * 7 );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) {
                                    ?>
                                    <div class="dpb-popup <?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>">
                                        <?php 
                                    if ( !empty( $promotional_banner_image ) ) {
                                        ?>
                                            <img src="<?php 
                                        echo esc_url( $promotional_banner_image );
                                        ?>"/>
                                            <?php 
                                    }
                                    ?>
                                        <div class="dpb-popup-meta">
                                            <p>
                                            <?php 
                                    echo wp_kses_post( str_replace( array('<p>', '</p>'), '', $promotional_banner_description ) );
                                    if ( !empty( $promotional_banner_button_group ) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
                                                    <a href="<?php 
                                            echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] );
                                            ?>" target="_blank"><?php 
                                            echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] );
                                            ?></a>
                                                    <?php 
                                        }
                                    }
                                    ?>
                                            </p>
                                        </div>
                                        <a href="javascript:void(0);" data-bar-id="<?php 
                                    echo esc_attr( $promotional_banner_id );
                                    ?>" data-popup-name="<?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_attr( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                    </div>
                                    <?php 
                                }
                            }
                        } else {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) {
                                    ?>
                                    <div class="dpb-popup <?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>">
                                        <?php 
                                    if ( !empty( $promotional_banner_image ) ) {
                                        ?>
                                            <img src="<?php 
                                        echo esc_url( $promotional_banner_image );
                                        ?>"/>
                                            <?php 
                                    }
                                    ?>
                                        <div class="dpb-popup-meta">
                                            <p>
                                                <?php 
                                    echo wp_kses_post( str_replace( array('<p>', '</p>'), '', $promotional_banner_description ) );
                                    if ( !empty( $promotional_banner_button_group ) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
                                                        <a href="<?php 
                                            echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] );
                                            ?>" target="_blank"><?php 
                                            echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] );
                                            ?></a>
                                                        <?php 
                                        }
                                    }
                                    ?>
                                            </p>
                                        </div>
                                        <a href="javascript:void(0);" data-bar-id="<?php 
                                    echo esc_attr( $promotional_banner_id );
                                    ?>" data-popup-name="<?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                    </div>
                                    <?php 
                                }
                            }
                        }
                    }
                }
            }
            echo '</div>';
        }
    }

    /**
     * Get and save plugin setup wizard data
     * 
     * @since    3.9.3
     * 
     */
    public function wcdg_plugin_setup_wizard_submit() {
        check_ajax_referer( 'wizard_ajax_nonce', 'nonce' );
        $survey_list = filter_input( INPUT_GET, 'survey_list', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !empty( $survey_list ) && 'Select One' !== $survey_list ) {
            update_option( 'wcdg_where_hear_about_us', $survey_list );
        }
        wp_die();
    }

    /**
     * Send setup wizard data to sendinblue
     * 
     * @since    3.9.3
     * 
     */
    public function wcdg_send_wizard_data_after_plugin_activation() {
        $send_wizard_data = filter_input( INPUT_GET, 'send-wizard-data', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $send_wizard_data ) && !empty( $send_wizard_data ) ) {
            if ( !get_option( 'wcdg_data_submited_in_sendiblue' ) ) {
                $wcdg_where_hear = get_option( 'wcdg_where_hear_about_us' );
                $get_user = wcfdg_fs()->get_user();
                $data_insert_array = array();
                if ( isset( $get_user ) && !empty( $get_user ) ) {
                    $data_insert_array = array(
                        'user_email'              => $get_user->email,
                        'ACQUISITION_SURVEY_LIST' => $wcdg_where_hear,
                    );
                }
                $feedback_api_url = WCDG_STORE_URL . '/wp-json/dotstore-sendinblue-data/v2/dotstore-sendinblue-data?' . wp_rand();
                $query_url = $feedback_api_url . '&' . http_build_query( $data_insert_array );
                if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
                    $response = vip_safe_wp_remote_get(
                        $query_url,
                        3,
                        1,
                        20
                    );
                } else {
                    $response = wp_remote_get( $query_url );
                    //phpcs:ignore
                }
                if ( !is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
                    update_option( 'wcdg_data_submited_in_sendiblue', '1' );
                    delete_option( 'wcdg_where_hear_about_us' );
                }
            }
        }
    }

    /**
     * Master Settings Page
     */
    public function wcdg_master_settings_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdg-master-settings.php';
    }

}
