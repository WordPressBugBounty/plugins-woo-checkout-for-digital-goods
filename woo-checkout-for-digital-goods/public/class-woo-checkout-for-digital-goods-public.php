<?php

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
class Woo_Checkout_For_Digital_Goods_Public {
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
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/woo-checkout-for-digital-goods-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/woo-checkout-for-digital-goods-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Override woocommerce label and placeholder with our plugin changes.
     * 
     * @since   3.7.0
     */
    public function wcdg_prepare_country_locale( $fields ) {
        $override_label = $override_ph = true;
        if ( is_array( $fields ) ) {
            foreach ( $fields as $key => $props ) {
                if ( $override_label && isset( $props['label'] ) ) {
                    unset($fields[$key]['label']);
                }
                if ( $override_ph && isset( $props['placeholder'] ) ) {
                    unset($fields[$key]['placeholder']);
                }
            }
        }
        return $fields;
    }

    /**
     * Function for remove checkout fields.
     */
    public function wcdg_override_checkout_fields( $fields ) {
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $woo_checkout_field_array = ( isset( $woo_checkout_unserlize_array['wcdg_chk_field'] ) ? $woo_checkout_unserlize_array['wcdg_chk_field'] : '' );
        $woo_checkout_order_note = ( isset( $woo_checkout_unserlize_array['wcdg_chk_order_note'] ) ? $woo_checkout_unserlize_array['wcdg_chk_order_note'] : '' );
        $temp_product_flag = 1;
        // Return fields if cart is null
        if ( is_null( WC()->cart ) ) {
            return $fields;
        }
        // basic checks
        foreach ( WC()->cart->get_cart() as $values ) {
            $_product = $values['data'];
            if ( !$_product->is_virtual() && !$_product->is_downloadable() ) {
                $temp_product_flag = 0;
                break;
            }
        }
        if ( 0 === $temp_product_flag ) {
            return $fields;
        } else {
            //Hide checkout shiping fields
            add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
            //Hide checkout order note field
            if ( !empty( $woo_checkout_order_note ) ) {
                unset($fields['order']['order_comments']);
                add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
            }
            //Hide checkout select billing fields
            $exclude_all = false;
            if ( !empty( $woo_checkout_field_array ) ) {
                foreach ( $woo_checkout_field_array as $woo_checkout_field ) {
                    if ( !is_array( $woo_checkout_field ) ) {
                        $exclude_all = true;
                        break;
                    } else {
                        if ( array_search( "on", $woo_checkout_field, true ) ) {
                            $exclude_all = true;
                            break;
                        }
                    }
                }
            }
            if ( $exclude_all ) {
                foreach ( $woo_checkout_field_array as $key => $values ) {
                    if ( !is_array( $woo_checkout_field ) ) {
                        unset($fields['billing'][$values]);
                    } else {
                        if ( "on" === $values['enable'] ) {
                            unset($fields['billing'][$key]);
                        } else {
                            foreach ( $values as $override_k => $override_v ) {
                                $fields['billing'][$key][$override_k] = $override_v;
                            }
                        }
                    }
                }
            } else {
                unset($fields['billing']['billing_first_name']);
                unset($fields['billing']['billing_last_name']);
                unset($fields['billing']['billing_company']);
                unset($fields['billing']['billing_address_1']);
                unset($fields['billing']['billing_address_2']);
                unset($fields['billing']['billing_city']);
                unset($fields['billing']['billing_postcode']);
                unset($fields['billing']['billing_country']);
                unset($fields['billing']['billing_state']);
                unset($fields['billing']['billing_phone']);
                return $fields;
            }
        }
        return $fields;
    }

    /**
     * Function for insert quick checkout button after add to cart button.
     */
    public function wcdg_add_quick_checkout_after_add_to_cart_product_page() {
        $virtual_product = [];
        $is_virtual = [];
        $downloadable_product = [];
        $is_downloadable = [];
        $get_variations_id = [];
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        global $product;
        if ( 'wcdg_down_virtual' === $woo_checkout_unserlize_array['wcdg_chk_on'] ) {
            if ( $product->is_type( 'variable' ) || $product->is_type( 'variable-subscription' ) ) {
                // get variable product variations
                $get_variations = $product->get_available_variations();
                // check current variable product is virtual or not and get varitions id
                if ( isset( $get_variations ) && !empty( $get_variations ) ) {
                    foreach ( $get_variations as $get_variation ) {
                        $virtual_product[] = $get_variation['is_virtual'];
                    }
                }
                if ( isset( $virtual_product ) && !empty( $virtual_product ) ) {
                    foreach ( $virtual_product as $single_virtual_product ) {
                        if ( $single_virtual_product ) {
                            $is_virtual[] = $single_virtual_product;
                        }
                    }
                }
                // check current variable product is downloadable or not
                if ( isset( $get_variations ) && !empty( $get_variations ) ) {
                    foreach ( $get_variations as $get_variation ) {
                        $downloadable_product[] = $get_variation['is_downloadable'];
                    }
                }
                if ( isset( $downloadable_product ) && !empty( $downloadable_product ) ) {
                    foreach ( $downloadable_product as $single_downloadable_product ) {
                        if ( $single_downloadable_product ) {
                            $is_downloadable[] = $single_downloadable_product;
                        }
                    }
                }
            }
            if ( $product->is_virtual( 'yes' ) || $product->is_downloadable( 'yes' ) || isset( $is_virtual ) && !empty( $is_virtual ) || isset( $is_downloadable ) && !empty( $is_downloadable ) ) {
                $addtocart_url = wc_get_checkout_url() . '?add-to-cart=' . $product->get_id();
                $button_class = 'single_add_to_cart_button button alt custom-checkout-btn';
                if ( $product->is_type( 'simple' ) && ($product->is_virtual( 'yes' ) || $product->is_downloadable( 'yes' )) ) {
                    ?>
                <script>
                jQuery(function($) {
                    var url    = '<?php 
                    echo esc_url( $addtocart_url );
                    ?>',
                        qty    = 'input.qty',
                        button = 'a.custom-checkout-btn';

                    // On input/change quantity event
                    $(qty).on('input change', function() {
                        $(button).attr('href', url + '&quantity=' + $(this).val() );
                    });
                });
                </script>
                <?php 
                } elseif ( $product->is_type( 'variable' ) ) {
                    $addtocart_url = wc_get_checkout_url() . '?add-to-cart=';
                    ?>
                <script>
                jQuery(function($) {
                    var url    = '<?php 
                    echo esc_url( $addtocart_url );
                    ?>',
                        vid    = 'input[name="variation_id"]',
                        pid    = 'input[name="product_id"]',
                        qty    = 'input.qty',
                        button = 'a.custom-checkout-btn';

                    // Once DOM is loaded
                    setTimeout( function(){
                        if( $(vid).val() != '' ){
                            $(button).attr('href', url + $(vid).val() + '&quantity=' + $(qty).val() );
                        }
                    }, 300 );

                    // On input/change quantity event
                    $(qty).on('input change', function() {
                        if( $(vid).val() != '' ){
                            $(button).attr('href', url + $(vid).val() + '&quantity=' + $(this).val() );
                        }
                    });

                    // On select attribute field change event
                    $('.variations_form').on('change blur', 'table.variations select', function() {
                        if( $(vid).val() != '' ){
                            $(button).attr('href', url + $(vid).val() + '&quantity=' + $(qty).val() );
                        }
                    });
                });
                </script>
                <?php 
                }
                $quick_checkout_text = apply_filters( 'quick_checkout_text', esc_html__( "Quick Checkout", 'woo-checkout-for-digital-goods' ) );
                echo '<a href="' . esc_url( $addtocart_url ) . '" class="' . esc_attr( $button_class ) . '">' . esc_html__( $quick_checkout_text, 'woo-checkout-for-digital-goods' ) . '</a>';
            }
        }
    }

    /**
     * Quick Checkout Button on shop page
     */
    public function wcdg_add_quick_checkout_after_add_to_cart_shop_page() {
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        global $product;
        if ( 'wcdg_down_virtual' === $woo_checkout_unserlize_array['wcdg_chk_on'] ) {
            if ( $product->is_virtual( 'yes' ) || $product->is_downloadable( 'yes' ) ) {
                // get the current post/product ID
                $current_product_id = get_the_ID();
                // get the product based on the ID
                $product = wc_get_product( $current_product_id );
                // get the "Checkout Page" URL
                $checkout_url = wc_get_checkout_url();
                // run on simple & subscription products
                if ( $product->is_type( 'simple' ) || $product->is_type( 'subscription' ) ) {
                    $quick_checkout_text = apply_filters( 'quick_checkout_text', esc_html__( "Quick Checkout", 'woo-checkout-for-digital-goods' ) );
                    $url = $checkout_url . '?add-to-cart=' . $current_product_id;
                    echo '<a href="' . esc_url( $url ) . '" class="single_add_to_cart_button button alt">' . esc_html( $quick_checkout_text ) . '</a>';
                }
            }
        }
    }

    /**
     * Delay account for new user registration
     */
    public function wcdg_delay_register_guests( $order_id ) {
        // get all the order data
        $order = wc_get_order( $order_id );
        $order_data = $order->get_data();
        //get the user email from the order
        $order_email = $order_data['billing']['email'];
        // check if there are any users with the billing email as user or email
        $email = email_exists( $order_email );
        $user = username_exists( $order_email );
        // if guest checkout enabled
        $wegc = get_option( 'woocommerce_enable_guest_checkout' );
        if ( 'yes' !== $wegc ) {
            // if the UID is null, then it's a guest checkout
            if ( false === $user && false === $email ) {
                // random password with 12 chars
                $random_password = wp_generate_password();
                // create new user with email as username & newly created pw
                $user_id = wp_create_user( $order_email, $random_password, $order_email );
                $user_id_role = new WP_User($user_id);
                $user_id_role->set_role( 'customer' );
                wc_update_new_customer_past_orders( $user_id );
                $wc_emails = WC()->mailer()->get_emails();
                $wc_emails['WC_Email_Customer_New_Account']->trigger( $user_id, $random_password, true );
                echo '<label class="wcdg_update">' . esc_html__( 'Please check your email for login details and update your remaining billing details.', 'woo-checkout-for-digital-goods' ) . '</label>';
            }
            $create_account_flag = apply_filters( 'create_account_flag', true );
            if ( true === $create_account_flag ) {
                echo '<a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" class="button wcdg_delay_account">' . esc_html__( 'My Account', 'woo-checkout-for-digital-goods' ) . '</a>';
            }
        }
    }

}
