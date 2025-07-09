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
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;
use Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils;
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
        $this->register_custom_field_hooks();
    }

    public function register_custom_field_hooks() {
        // Show custom fields in order emails
        add_filter(
            'woocommerce_email_order_meta_fields',
            function ( $fields, $sent_to_admin, $order ) {
                $settings = get_option( 'wcdg_checkout_setting', array() );
                if ( !empty( $settings['wcdg_chk_field'] ) ) {
                    foreach ( $settings['wcdg_chk_field'] as $key => $field ) {
                        if ( !empty( $field['show_in_email'] ) ) {
                            $meta_key = $key;
                            $label = ( isset( $field['label'] ) ? $field['label'] : $key );
                            $value = get_post_meta( $order->get_id(), $meta_key, true );
                            if ( $value !== '' ) {
                                $fields[$meta_key] = array(
                                    'label' => $label,
                                    'value' => $value,
                                );
                            }
                        }
                    }
                }
                return $fields;
            },
            10,
            3
        );
        // Show custom fields in order details (thank you page and admin)
        add_action( 'woocommerce_order_details_after_customer_details', function ( $order ) {
            $settings = get_option( 'wcdg_checkout_setting', array() );
            if ( !empty( $settings['wcdg_chk_field'] ) ) {
                echo '<section class="woocommerce-customer-details"><h3>' . esc_html__( 'Additional Information', 'woo-checkout-for-digital-goods' ) . '</h3><table class="shop_table customer_details">';
                foreach ( $settings['wcdg_chk_field'] as $key => $field ) {
                    if ( !empty( $field['show_in_order'] ) ) {
                        $meta_key = $key;
                        $label = ( isset( $field['label'] ) ? $field['label'] : $key );
                        $value = get_post_meta( $order->get_id(), $meta_key, true );
                        if ( $value !== '' ) {
                            echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
                        }
                    }
                }
                echo '</table></section>';
            }
        } );
        // Show custom fields in admin order details
        add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
            $settings = get_option( 'wcdg_checkout_setting', array() );
            if ( !empty( $settings['wcdg_chk_field'] ) ) {
                foreach ( $settings['wcdg_chk_field'] as $key => $field ) {
                    if ( !empty( $field['show_in_order'] ) ) {
                        $meta_key = $key;
                        $label = ( isset( $field['label'] ) ? $field['label'] : $key );
                        $value = get_post_meta( $order->get_id(), $meta_key, true );
                        if ( $value !== '' ) {
                            echo '<p><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ) . '</p>';
                        }
                    }
                }
            }
        } );
        // Save custom fields to order meta (legacy, for non-HPOS)
        add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
            $settings = get_option( 'wcdg_checkout_setting', array() );
            $default_fields = array();
            if ( function_exists( 'wcdg_get_default_settings' ) ) {
                $default_settings = wcdg_get_default_settings();
                if ( isset( $default_settings['wcdg_chk_field'] ) ) {
                    $default_fields = array_keys( $default_settings['wcdg_chk_field'] );
                }
            }
            if ( !empty( $settings['wcdg_chk_field'] ) ) {
                foreach ( $settings['wcdg_chk_field'] as $key => $field ) {
                    if ( in_array( $key, $default_fields, true ) ) {
                        continue;
                        // Skip default fields
                    }
                    if ( isset( $_POST[$key] ) ) {
                        // phpcs:ignore
                        $value = sanitize_text_field( $_POST[$key] );
                        // phpcs:ignore
                        update_post_meta( $order_id, $key, $value );
                    }
                }
            }
        } );
        // Save custom fields to order meta (HPOS compatible)
        add_action(
            'woocommerce_checkout_create_order',
            function ( $order, $data ) {
                $settings = get_option( 'wcdg_checkout_setting', array() );
                $default_fields = array();
                if ( function_exists( 'wcdg_get_default_settings' ) ) {
                    $default_settings = wcdg_get_default_settings();
                    if ( isset( $default_settings['wcdg_chk_field'] ) ) {
                        $default_fields = array_keys( $default_settings['wcdg_chk_field'] );
                    }
                }
                if ( !empty( $settings['wcdg_chk_field'] ) ) {
                    foreach ( $settings['wcdg_chk_field'] as $key => $field ) {
                        if ( in_array( $key, $default_fields, true ) ) {
                            continue;
                            // Skip default fields
                        }
                        if ( isset( $_POST[$key] ) ) {
                            // phpcs:ignore
                            $value = sanitize_text_field( $_POST[$key] );
                            // phpcs:ignore
                            $order->update_meta_data( $key, $value );
                        }
                    }
                }
            },
            10,
            2
        );
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
        // Add inline style for hide order notes
        $this->wcdg_hide_order_notes_field_with_block();
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
        $temp_product_flag = 1;
        // Return fields if cart is null
        $get_cart = WC()->cart;
        if ( is_null( $get_cart ) ) {
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
        }
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
        if ( is_null( WC()->cart ) ) {
            return $fields;
        }
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
                        if ( isset( $values['enable'] ) && "on" === $values['enable'] ) {
                            unset($fields['billing'][$key]);
                        } else {
                            // Convert select options to WC format before adding to $fields['billing']
                            if ( isset( $values['type'] ) && $values['type'] === 'select' && !empty( $values['options'] ) && is_array( $values['options'] ) ) {
                                $options = [];
                                foreach ( $values['options'] as $opt ) {
                                    if ( is_array( $opt ) && isset( $opt['value'] ) && isset( $opt['label'] ) ) {
                                        $options[$opt['value']] = $opt['label'];
                                    }
                                }
                                $values['options'] = $options;
                            }
                            foreach ( $values as $override_k => $override_v ) {
                                // If the key is 'class', set as array
                                if ( $override_k === 'class' && !empty( $override_v ) ) {
                                    $fields['billing'][$key]['class'] = ( is_array( $override_v ) ? $override_v : preg_split( '/\\s+/', trim( $override_v ) ) );
                                } elseif ( $override_k === 'type' && $override_v === 'checkbox' ) {
                                    // Ensure checkbox uses default value as its checked value and value
                                    $checkbox_value = ( isset( $values['default'] ) && $values['default'] !== '' ? $values['default'] : '1' );
                                    $fields['billing'][$key]['type'] = 'checkbox';
                                    $fields['billing'][$key]['wcdg_custom_checkbox_value'] = $checkbox_value;
                                    $fields['billing'][$key]['label'] = ( isset( $values['label'] ) ? $values['label'] : $key );
                                    $fields['billing'][$key]['default'] = $checkbox_value;
                                } else {
                                    $fields['billing'][$key][$override_k] = $override_v;
                                }
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
            // --- Custom: Set priority for billing fields based on backend order ---
            $billing_fields_order = get_option( 'wcdg_billing_fields_order', '' );
            if ( !empty( $billing_fields_order ) && !empty( $fields['billing'] ) ) {
                $ordered_keys = explode( ',', $billing_fields_order );
                $priority = 1;
                foreach ( $ordered_keys as $key ) {
                    if ( isset( $fields['billing'][$key] ) ) {
                        $fields['billing'][$key]['priority'] = $priority++;
                    }
                }
                // For any fields not in the saved order, assign a higher priority
                foreach ( $fields['billing'] as $key => &$field ) {
                    if ( !isset( $field['priority'] ) ) {
                        $field['priority'] = $priority++;
                    }
                }
            }
            // --- End Custom ---
            uasort( $fields['billing'], function ( $a, $b ) {
                return ($a['priority'] ?? 99) <=> ($b['priority'] ?? 99);
            } );
            return $fields;
        }
        return $fields;
    }

    /**
     * Function for remove checkout fields with new Checkout Blocks.
     */
    public function wcdg_override_checkout_fields_with_blocks( $locale ) {
        // Check if the Checkout Block is being used
        if ( class_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class ) && method_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class, 'is_checkout_block_default' ) && !CartCheckoutUtils::is_checkout_block_default() ) {
            return $locale;
        }
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $woo_checkout_field_array = ( isset( $woo_checkout_unserlize_array['wcdg_chk_field'] ) ? $woo_checkout_unserlize_array['wcdg_chk_field'] : array() );
        $temp_product_flag = 1;
        $countries = array_merge( array_keys( WC()->countries->get_shipping_countries() ), array_keys( WC()->countries->get_allowed_countries() ) );
        $countries = array_unique( $countries );
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
        // Return fields if cart is null
        $get_cart = WC()->cart;
        if ( is_null( $get_cart ) ) {
            return $locale;
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
            return $locale;
        } else {
            if ( $exclude_all ) {
                foreach ( $woo_checkout_field_array as $key => $values ) {
                    $key = str_replace( 'billing_', '', $key );
                    if ( !is_array( $values ) ) {
                        foreach ( $countries as $country ) {
                            if ( !isset( $locale[$country] ) ) {
                                $locale[$country] = array();
                            }
                            if ( $key !== 'country' ) {
                                $locale[$country][$key] = array(
                                    'required' => false,
                                    'hidden'   => true,
                                );
                            }
                        }
                    } else {
                        if ( isset( $values['enable'] ) && 'on' === $values['enable'] ) {
                            foreach ( $countries as $country ) {
                                if ( !isset( $locale[$country] ) ) {
                                    $locale[$country] = array();
                                }
                                if ( $key !== 'country' ) {
                                    $locale[$country][$key] = array(
                                        'required' => false,
                                        'hidden'   => true,
                                    );
                                }
                            }
                        }
                    }
                }
            } else {
                foreach ( $countries as $country ) {
                    if ( !isset( $locale[$country] ) ) {
                        $locale[$country] = array();
                    }
                    $locale[$country]['first_name'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['last_name'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['address_1'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['address_2'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['postcode'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['city'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['state'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                    $locale[$country]['phone'] = array(
                        'required' => false,
                        'hidden'   => true,
                    );
                }
                return $locale;
            }
        }
        return $locale;
    }

    /**
     * Function for update block address format
     */
    public function wcdg_change_checkout_block_address_format( $formats ) {
        if ( !is_cart() || !is_checkout() ) {
            return $formats;
        }
        // Check if the Checkout Block is being used
        if ( class_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class ) && method_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class, 'is_checkout_block_default' ) && !CartCheckoutUtils::is_checkout_block_default() ) {
            return $formats;
        }
        if ( is_array( $formats ) ) {
            foreach ( $formats as $key => $format ) {
                $formats[$key] = "{first_name} {last_name}\n{country}";
            }
        }
        return $formats;
    }

    /**
     * Function for update block fields labels
     */
    public function wcdg_update_default_fields_data_with_block() {
        if ( !class_exists( 'Automattic\\WooCommerce\\Blocks\\Package' ) || !class_exists( 'Automattic\\WooCommerce\\Blocks\\Assets\\AssetDataRegistry' ) || !class_exists( 'Automattic\\WooCommerce\\Blocks\\Domain\\Services\\CheckoutFields' ) ) {
            return;
        }
        // basic checks
        $temp_product_flag = 1;
        if ( isset( WC()->session ) && WC()->session->has_session() ) {
            $cart_contents = WC()->session->get( 'cart', array() );
            foreach ( $cart_contents as $values ) {
                if ( isset( $values['product_id'] ) ) {
                    $_product = wc_get_product( $values['product_id'] );
                    if ( $_product && !$_product->is_virtual() && !$_product->is_downloadable() ) {
                        $temp_product_flag = 0;
                        break;
                    }
                }
            }
        }
        // If cart contains non-virtual/downloadable products, return default fields
        if ( $temp_product_flag === 0 ) {
            return;
        }
        // Modify checkout fields
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $woo_checkout_field_array = ( isset( $woo_checkout_unserlize_array['wcdg_chk_field'] ) ? $woo_checkout_unserlize_array['wcdg_chk_field'] : array() );
        $checkout_fields = Package::container()->get( CheckoutFields::class );
        $asset_data_registry = Package::container()->get( AssetDataRegistry::class );
        $default_address_fields = $checkout_fields->get_core_fields();
        if ( !empty( $default_address_fields ) && is_array( $default_address_fields ) ) {
            // --- Custom: Reorder billing fields based on admin setting ---
            $billing_fields_order = get_option( 'wcdg_billing_fields_order', '' );
            if ( !empty( $billing_fields_order ) ) {
                $ordered_keys = array();
                foreach ( explode( ',', $billing_fields_order ) as $key ) {
                    // Remove 'billing_' prefix for block fields
                    $short_key = preg_replace( '/^billing_/', '', $key );
                    $ordered_keys[] = $short_key;
                }
                $reordered_fields = array();
                foreach ( $ordered_keys as $key ) {
                    if ( isset( $default_address_fields[$key] ) ) {
                        $reordered_fields[$key] = $default_address_fields[$key];
                        unset($default_address_fields[$key]);
                    }
                }
                // Append any fields not in the saved order (new fields)
                $default_address_fields = array_merge( $reordered_fields, $default_address_fields );
            }
            // --- End Custom ---
            foreach ( $default_address_fields as $key => &$field ) {
                if ( $key === 'email' ) {
                    continue;
                }
                // Convert key to match format in `$woo_checkout_field_array`
                $billing_key = 'billing_' . $key;
                if ( isset( $woo_checkout_field_array[$billing_key]['label'] ) ) {
                    $field['label'] = $woo_checkout_field_array[$billing_key]['label'];
                    $field['optionalLabel'] = $woo_checkout_field_array[$billing_key]['label'];
                }
                if ( $key !== 'country' && isset( $woo_checkout_field_array[$billing_key]['enable'] ) && 'on' === $woo_checkout_field_array[$billing_key]['enable'] ) {
                    $field['required'] = false;
                    $field['hidden'] = true;
                }
                // Set class if provided in backend
                if ( isset( $woo_checkout_field_array[$billing_key]['class'] ) && !empty( $woo_checkout_field_array[$billing_key]['class'] ) ) {
                    $class_val = $woo_checkout_field_array[$billing_key]['class'];
                    $field['class'] = ( is_array( $class_val ) ? $class_val : preg_split( '/\\s+/', trim( $class_val ) ) );
                }
            }
            unset($field);
        }
        $asset_data_registry->add( 'defaultFields', array_merge( $default_address_fields, $checkout_fields->get_additional_fields() ) );
    }

    /**
     * Function for hide order notes field
     */
    public function wcdg_hide_order_notes_field_with_block() {
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $woo_checkout_order_note = ( isset( $woo_checkout_unserlize_array['wcdg_chk_order_note'] ) ? $woo_checkout_unserlize_array['wcdg_chk_order_note'] : '' );
        $temp_product_flag = 1;
        // Return if cart is null
        if ( is_null( WC()->cart ) ) {
            return;
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
            return;
        } else {
            //Hide checkout order note field
            if ( !empty( $woo_checkout_order_note ) ) {
                // Hide button from single page
                $hide_order_notes = '.wc-block-checkout__main .wc-block-checkout__order-notes{display:none!important;}';
                wp_add_inline_style( $this->plugin_name, $hide_order_notes );
            }
        }
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
        $wcdg_chk_btn_label = ( isset( $woo_checkout_unserlize_array['wcdg_chk_btn_label'] ) ? $woo_checkout_unserlize_array['wcdg_chk_btn_label'] : '' );
        $quick_checkout_text = ( !empty( $wcdg_chk_btn_label ) ? $wcdg_chk_btn_label : apply_filters( 'quick_checkout_text', esc_html__( "Quick Checkout", 'woo-checkout-for-digital-goods' ) ) );
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
                if ( wp_is_block_theme() ) {
                    echo '<a href="' . esc_url( $addtocart_url ) . '" class="wp-element-button ' . esc_attr( $button_class ) . '">' . esc_html( $quick_checkout_text ) . '</a>';
                } else {
                    echo '<a href="' . esc_url( $addtocart_url ) . '" class="' . esc_attr( $button_class ) . '">' . esc_html( $quick_checkout_text ) . '</a>';
                }
            }
        }
    }

    /**
     * Quick Checkout Button on shop page
     */
    public function wcdg_add_quick_checkout_after_add_to_cart_shop_page() {
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $wcdg_chk_btn_label = ( isset( $woo_checkout_unserlize_array['wcdg_chk_btn_label'] ) ? $woo_checkout_unserlize_array['wcdg_chk_btn_label'] : '' );
        $quick_checkout_text = ( !empty( $wcdg_chk_btn_label ) ? $wcdg_chk_btn_label : apply_filters( 'quick_checkout_text', esc_html__( "Quick Checkout", 'woo-checkout-for-digital-goods' ) ) );
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
                    $url = $checkout_url . '?add-to-cart=' . $current_product_id;
                    if ( wp_is_block_theme() ) {
                        echo '<div class="wp-block-button align-center wc-block-components-product-button">';
                        echo '<a href="' . esc_url( $url ) . '" class="wp-element-button single_add_to_cart_button button alt">' . esc_html( $quick_checkout_text ) . '</a>';
                        echo '</div>';
                    } else {
                        echo '<a href="' . esc_url( $url ) . '" class=" single_add_to_cart_button button alt">' . esc_html( $quick_checkout_text ) . '</a>';
                    }
                }
            }
        }
    }

    /**
     * Change default Add to Cart button label
     */
    public function wcdg_change_add_to_cart_btn_text( $text, $product ) {
        // Get settings
        $woo_checkout_unserlize_array = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
        $wcdg_enable_cart_btn_label = ( isset( $woo_checkout_unserlize_array['wcdg_enable_cart_btn_label'] ) ? $woo_checkout_unserlize_array['wcdg_enable_cart_btn_label'] : '' );
        // Custom button label from settings
        $wcdg_cart_btn_label = ( isset( $woo_checkout_unserlize_array['wcdg_cart_btn_label'] ) && !empty( $woo_checkout_unserlize_array['wcdg_cart_btn_label'] ) ? $woo_checkout_unserlize_array['wcdg_cart_btn_label'] : __( 'Add to cart', 'woo-checkout-for-digital-goods' ) );
        $final_button = $wcdg_cart_btn_label;
        // Ensure the feature is enabled in settings
        if ( empty( $wcdg_enable_cart_btn_label ) || 'on' !== $wcdg_enable_cart_btn_label ) {
            $final_button = $text;
        }
        // On the shop page, keep WooCommerce's default text for grouped products
        if ( !is_singular( 'product' ) && $product->is_type( 'grouped' ) ) {
            $final_button = $text;
        }
        // Preserve default WooCommerce behavior for out-of-stock or non-purchasable products
        if ( !$product->is_purchasable() || !$product->is_in_stock() ) {
            $final_button = $text;
        }
        // Keep default "Select options" text for variable products on shop pages
        if ( !is_singular( 'product' ) && $product->is_type( 'variable' ) ) {
            $final_button = $text;
        }
        // Apply custom button text for grouped products ONLY on the single product page
        if ( is_singular( 'product' ) && $product->is_type( 'grouped' ) ) {
            $final_button = $wcdg_cart_btn_label;
        }
        return $final_button;
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

// Ensure address field priorities are set in default address fields as well
add_filter( 'woocommerce_default_address_fields', function ( $fields ) {
    $billing_fields_order = get_option( 'wcdg_billing_fields_order', '' );
    if ( !empty( $billing_fields_order ) ) {
        $ordered_keys = explode( ',', $billing_fields_order );
        $priority = 1;
        foreach ( $ordered_keys as $key ) {
            // Remove 'billing_' prefix for default address fields
            $short_key = preg_replace( '/^billing_/', '', $key );
            if ( isset( $fields[$short_key] ) ) {
                $fields[$short_key]['priority'] = $priority++;
            }
        }
        // For any fields not in the saved order, assign a higher priority
        foreach ( $fields as $key => &$field ) {
            if ( !isset( $field['priority'] ) ) {
                $field['priority'] = $priority++;
            }
        }
    }
    return $fields;
}, 20 );
// Custom render for custom checkbox fields to use admin Default Value as value
add_filter(
    'woocommerce_form_field_checkbox',
    function (
        $field,
        $key,
        $args,
        $value
    ) {
        if ( !empty( $args['wcdg_custom_checkbox_value'] ) ) {
            $checked = checked( $value, $args['wcdg_custom_checkbox_value'], false );
            $required = ( !empty( $args['required'] ) ? 'aria-required="true" required' : '' );
            $classes = array(
                'form-row',
                'wcdg-' . $key,
                'validate-required',
                'checkbox'
            );
            if ( !empty( $args['class'] ) ) {
                $classes = array_merge( $classes, (array) $args['class'] );
            }
            $label = esc_html( $args['label'] );
            if ( !empty( $args['required'] ) ) {
                $label .= ' <span class="required" aria-hidden="true">*</span>';
            }
            $input_id = 'wcdg_' . esc_attr( $key );
            $input_name = esc_attr( $key );
            $field = sprintf(
                '<p class="%s" id="%s_field">' . '<span class="woocommerce-input-wrapper" style="display:flex;align-items:center;gap:6px;">' . '<input type="checkbox" class="input-checkbox %s" name="%s" id="%s" value="%s" %s %s />' . '<label for="%s" class="checkbox" style="margin:0;">%s</label>' . '</span>' . '</p>',
                esc_attr( implode( ' ', $classes ) ),
                esc_attr( $key ),
                esc_attr( implode( ' ', $args['input_class'] ?? [] ) ),
                $input_name,
                $input_id,
                esc_attr( $args['wcdg_custom_checkbox_value'] ),
                $checked,
                $required,
                $input_id,
                $label
            );
        }
        return $field;
    },
    10,
    4
);