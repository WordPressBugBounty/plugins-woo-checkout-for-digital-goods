<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
use Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils;
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
?>
    <div class="wcdg-main-left-section res-cl">
        <?php 
//Check array is multidimensional or not
function wcdg_is_multi(  $check_array  ) {
    $sj = array_filter( $check_array, 'is_array' );
    if ( count( $sj ) > 0 ) {
        return true;
    }
    return false;
}

if ( isset( $_POST['submit_setting'] ) ) {
    // verify nonce
    if ( !isset( $_POST['woo_checkout_digital_goods'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woo_checkout_digital_goods'] ) ), basename( __FILE__ ) ) ) {
        die( 'Failed security check' );
    } else {
        $general_setting_data = maybe_unserialize( get_option( 'wcdg_checkout_setting', array() ) );
        $get_wcdg_status = filter_input( INPUT_POST, 'wcdg_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_field = filter_input(
            INPUT_POST,
            'wcdg_chk_field',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $merged_fields = ( is_array( $get_wcdg_chk_field ) ? $get_wcdg_chk_field : [] );
        $get_wcdg_chk_order_note = filter_input( INPUT_POST, 'wcdg_chk_order_note', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_btn_label = filter_input( INPUT_POST, 'wcdg_chk_btn_label', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_cart_btn_label = filter_input( INPUT_POST, 'wcdg_cart_btn_label', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_on = filter_input( INPUT_POST, 'wcdg_chk_on', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_user_role_field = filter_input(
            INPUT_POST,
            'wcdg_user_role_field',
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $get_wcdg_allow_additional_field_update_flag = filter_input( INPUT_POST, 'wcdg_allow_additional_field_update_flag', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_billing_fields_order = ( isset( $_POST['wcdg_billing_fields_order'] ) ? sanitize_text_field( $_POST['wcdg_billing_fields_order'] ) : '' );
        $wcdg_custom_fields_data = ( isset( $_POST['wcdg_custom_fields_data'] ) ? $_POST['wcdg_custom_fields_data'] : array() );
        // phpcs:ignore
        $wcdg_new_billing_fields = ( isset( $_POST['wcdg_new_billing_fields'] ) ? $_POST['wcdg_new_billing_fields'] : '' );
        // phpcs:ignore
        // Handle new fields from JS
        if ( !empty( $wcdg_new_billing_fields ) ) {
            $new_fields = json_decode( stripslashes( $wcdg_new_billing_fields ), true );
            if ( is_array( $new_fields ) ) {
                foreach ( $new_fields as $nf ) {
                    $key = sanitize_key( $nf['name'] );
                    $field_arr = array(
                        'label'         => sanitize_text_field( $nf['label'] ),
                        'type'          => sanitize_text_field( $nf['type'] ),
                        'required'      => ( !empty( $nf['required'] ) ? 'on' : '' ),
                        'default'       => sanitize_text_field( $nf['default'] ),
                        'show_in_email' => ( !empty( $nf['show_in_email'] ) ? 1 : 0 ),
                        'show_in_order' => ( !empty( $nf['show_in_order'] ) ? 1 : 0 ),
                        'class'         => sanitize_text_field( $nf['class'] ),
                        'placeholder'   => sanitize_text_field( $nf['placeholder'] ),
                    );
                    // Handle select options
                    if ( $nf['type'] === 'select' && !empty( $nf['options'] ) && is_array( $nf['options'] ) ) {
                        $field_arr['options'] = array();
                        foreach ( $nf['options'] as $opt ) {
                            $field_arr['options'][] = array(
                                'label' => sanitize_text_field( $opt['label'] ),
                                'value' => sanitize_text_field( $opt['value'] ),
                            );
                        }
                    }
                    // Handle checkbox checked state
                    if ( $nf['type'] === 'checkbox' ) {
                        $field_arr['checked'] = ( !empty( $nf['checked'] ) ? 1 : 0 );
                    }
                    $merged_fields[$key] = $field_arr;
                }
            }
        }
        // Merge in custom field JSON data if present
        if ( !empty( $wcdg_custom_fields_data ) && is_array( $wcdg_custom_fields_data ) ) {
            foreach ( $wcdg_custom_fields_data as $key => $json ) {
                $custom = json_decode( stripslashes( $json ), true );
                if ( is_array( $custom ) ) {
                    // Merge/overwrite all properties from the custom field JSON
                    $merged_fields[$key] = array_merge( ( isset( $merged_fields[$key] ) && is_array( $merged_fields[$key] ) ? $merged_fields[$key] : [] ), $custom );
                }
            }
        }
        function sanitize_array(  &$array  ) {
            foreach ( $array as &$value ) {
                if ( !is_array( $value ) ) {
                    // sanitize if value is not an array
                    $value = sanitize_text_field( $value );
                } else {
                    // go inside this function again
                    sanitize_array( $value );
                }
            }
            return $array;
        }

        $general_setting_data['wcdg_status'] = ( !empty( $get_wcdg_status ) ? sanitize_text_field( $get_wcdg_status ) : '' );
        $general_setting_data['wcdg_chk_field'] = $merged_fields;
        $general_setting_data['wcdg_chk_order_note'] = ( !empty( $get_wcdg_chk_order_note ) ? sanitize_text_field( $get_wcdg_chk_order_note ) : '' );
        $general_setting_data['wcdg_chk_btn_label'] = ( !empty( $get_wcdg_chk_btn_label ) ? sanitize_text_field( $get_wcdg_chk_btn_label ) : '' );
        $general_setting_data['wcdg_cart_btn_label'] = ( !empty( $get_wcdg_cart_btn_label ) ? sanitize_text_field( $get_wcdg_cart_btn_label ) : '' );
        $general_setting_data['wcdg_chk_on'] = ( !empty( $get_wcdg_chk_on ) ? sanitize_text_field( $get_wcdg_chk_on ) : 'wcdg_down_virtual' );
        $general_setting_data['wcdg_user_role_field'] = ( !empty( $get_wcdg_user_role_field ) ? array_map( 'sanitize_text_field', $get_wcdg_user_role_field ) : '' );
        $general_setting_data['wcdg_allow_additional_field_update_flag'] = ( !empty( $get_wcdg_allow_additional_field_update_flag ) ? sanitize_text_field( $get_wcdg_allow_additional_field_update_flag ) : '' );
        update_option( 'wcdg_checkout_setting', $general_setting_data );
        // Save billing fields order separately
        update_option( 'wcdg_billing_fields_order', $get_wcdg_billing_fields_order );
    }
}
$wcdg_general_setting = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
$wcdg_status = ( isset( $wcdg_general_setting['wcdg_status'] ) && !empty( $wcdg_general_setting['wcdg_status'] ) ? 'checked' : '' );
$wcdg_ch_field = ( isset( $wcdg_general_setting['wcdg_chk_field'] ) && !empty( $wcdg_general_setting['wcdg_chk_field'] ) ? $wcdg_general_setting['wcdg_chk_field'] : array() );
$wcdg_chk_order_note = ( isset( $wcdg_general_setting['wcdg_chk_order_note'] ) && !empty( $wcdg_general_setting['wcdg_chk_order_note'] ) ? 'checked' : '' );
$wcdg_chk_btn_label = ( isset( $wcdg_general_setting['wcdg_chk_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_chk_btn_label'] ) ? $wcdg_general_setting['wcdg_chk_btn_label'] : '' );
$wcdg_cart_btn_label = ( isset( $wcdg_general_setting['wcdg_cart_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_cart_btn_label'] ) ? $wcdg_general_setting['wcdg_cart_btn_label'] : '' );
$wcdg_chk_on = ( isset( $wcdg_general_setting['wcdg_chk_on'] ) && !empty( $wcdg_general_setting['wcdg_chk_on'] ) ? $wcdg_general_setting['wcdg_chk_on'] : 'wcdg_down_virtual' );
$wcdg_user_role_field = ( isset( $wcdg_general_setting['wcdg_user_role_field'] ) && !empty( $wcdg_general_setting['wcdg_user_role_field'] ) ? $wcdg_general_setting['wcdg_user_role_field'] : '' );
$wcdg_allow_additional_field_update_flag = ( isset( $wcdg_general_setting['wcdg_allow_additional_field_update_flag'] ) && !empty( $wcdg_general_setting['wcdg_allow_additional_field_update_flag'] ) ? 'checked' : '' );
// Get saved billing fields order
$wcdg_billing_fields_order = get_option( 'wcdg_billing_fields_order', '' );
$ordered_field_keys = array();
if ( !empty( $wcdg_billing_fields_order ) ) {
    $ordered_field_keys = explode( ',', $wcdg_billing_fields_order );
}
if ( isset( $_POST['reset_master_settings'] ) ) {
    if ( !isset( $_POST['woo_checkout_digital_goods'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woo_checkout_digital_goods'] ) ), basename( __FILE__ ) ) ) {
        die( 'Failed security check' );
    }
    $existing_settings = get_option( 'wcdg_checkout_setting', array() );
    $defaults = ( function_exists( 'wcdg_get_default_settings' ) ? wcdg_get_default_settings() : array() );
    foreach ( $defaults as $key => $value ) {
        $existing_settings[$key] = $value;
    }
    update_option( 'wcdg_checkout_setting', $existing_settings );
    wp_redirect( add_query_arg( 'wcdg_reset', '1', menu_page_url( 'wcdg-general-setting', false ) ) );
    exit;
}
$wcdg_reset = filter_input( INPUT_GET, 'wcdg_reset', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( isset( $wcdg_reset ) && $wcdg_reset === '1' ) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings reset successfully', 'woo-checkout-for-digital-goods' ) . '</p></div>';
}
// Checkout block notice
if ( class_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class ) && method_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class, 'is_checkout_block_default' ) && CartCheckoutUtils::is_checkout_block_default() ) {
    $hide_checkout_notice = filter_input( INPUT_GET, 'wcdg-hide-checkout-notice', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $checkout_notice_nonce = filter_input( INPUT_GET, '_wcdg_checkout_notice_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    if ( isset( $hide_checkout_notice ) && sanitize_text_field( $hide_checkout_notice ) === 'wcdg-hide-checkout-note' && wp_verify_nonce( sanitize_text_field( $checkout_notice_nonce ), 'wcdg_checkout_notices_nonce' ) ) {
        // Set transient for three months
        set_transient( 'wcdg-hide-checkout-notice', true, 3 * 30 * 24 * 60 * 60 );
    }
    /* Check transient, if available display notice */
    if ( !get_transient( 'wcdg-hide-checkout-notice' ) ) {
        ?>
                <div class="wcdg-checkout-notice">
                    <p><?php 
        echo esc_html_e( 'The billing country field cannot be removed when using the new WooCommerce checkout blocks. If you want to remove it completely, switch to the classic shortcode-based checkout.', 'woo-checkout-for-digital-goods' );
        ?></p>
                    <a class="notice-dismiss" href="<?php 
        echo esc_url( wp_nonce_url( add_query_arg( 'wcdg-hide-checkout-notice', 'wcdg-hide-checkout-note' ), 'wcdg_checkout_notices_nonce', '_wcdg_checkout_notice_nonce' ) );
        ?>"></a>
                </div>
                <div class="wcdg-checkout-notice">
                    <p><?php 
        echo esc_html_e( 'Field sorting and custom fields are not supported in WooCommerce Checkout Blocks. Use classic checkout (shortcode) for full compatibility.', 'woo-checkout-for-digital-goods' );
        ?></p>
                </div>
                <?php 
    }
}
// Add this after the checkout block notices, before the form
if ( class_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class ) && method_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class, 'is_checkout_block_default' ) && CartCheckoutUtils::is_checkout_block_default() ) {
    echo '<style>.wcdg-drag-handle { pointer-events: none; opacity: 0.4; cursor: not-allowed; }</style>';
}
?>
        <form method="POST" name="" action="">
            <?php 
wp_nonce_field( basename( __FILE__ ), 'woo_checkout_digital_goods' );
?>
            <div class="wcdg-checkout-billing-fields">
                <h2><?php 
esc_html_e( 'Checkout Billing Fields', 'woo-checkout-for-digital-goods' );
?>
                    <!-- Add New Field Button and Modal -->
                    <?php 
?>
                </h2>
                <table id="thwcfd_checkout_fields" class="wc_gateways widefat thpladmin_fields_table" cellspacing="0">
                    <thead>
                        <tr>
                        <?php 
?>
                            <th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" /></th>
                            <th class="name"><?php 
esc_html_e( 'Name', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Label', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Class', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Placeholder', 'woo-checkout-for-digital-goods' );
?></th>
                            <th class="status"><?php 
esc_html_e( 'Excluded', 'woo-checkout-for-digital-goods' );
?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                             <?php 
?>
                            <th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" /></th>
                            <th class="name"><?php 
esc_html_e( 'Name', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Label', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Class', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Placeholder', 'woo-checkout-for-digital-goods' );
?></th>
                            <th class="status"><?php 
esc_html_e( 'Excluded', 'woo-checkout-for-digital-goods' );
?></th>
                        </tr>
                    </tfoot>
                    <tbody class="ui-sortable">
                        <?php 
$checkout_obj = new WC_Countries();
$store_country = $checkout_obj->get_base_country();
$wcdg_default_fields = $checkout_obj->get_address_fields( $store_country, 'billing_' );
if ( !isset( $wcdg_default_fields['billing_company'] ) ) {
    $wcdg_default_fields['billing_company'] = ( isset( $wcdg_default_fields['billing_company'] ) ? $wcdg_default_fields['billing_company'] : [] );
}
unset($wcdg_default_fields['billing_email']);
// Reorder fields if order is saved
if ( !empty( $ordered_field_keys ) ) {
    $reordered_fields = array();
    foreach ( $ordered_field_keys as $field_key ) {
        if ( isset( $wcdg_default_fields[$field_key] ) ) {
            $reordered_fields[$field_key] = $wcdg_default_fields[$field_key];
            unset($wcdg_default_fields[$field_key]);
        }
    }
    // Append any fields not in the saved order (new fields)
    $wcdg_default_fields = array_merge( $reordered_fields, $wcdg_default_fields );
}
$wcdg_fields = $wcdg_ch_field;
$display_keys = array();
if ( !empty( $ordered_field_keys ) ) {
    $display_keys = $ordered_field_keys;
    // Append any new fields not in the order
    foreach ( array_keys( $wcdg_fields ) as $field_key ) {
        if ( !in_array( $field_key, $display_keys, true ) ) {
            $display_keys[] = $field_key;
        }
    }
} else {
    $display_keys = array_keys( $wcdg_fields );
}
foreach ( $display_keys as $wcdg_field_k ) {
    if ( !isset( $wcdg_fields[$wcdg_field_k] ) ) {
        continue;
    }
    $wcdg_field_v = $wcdg_fields[$wcdg_field_k];
    $label = ( isset( $wcdg_field_v['label'] ) ? $wcdg_field_v['label'] : '' );
    $class = ( isset( $wcdg_field_v['class'] ) && !empty( $wcdg_field_v['class'] ) ? $wcdg_field_v['class'] : 'form-row' );
    $placeholder = ( isset( $wcdg_field_v['placeholder'] ) ? $wcdg_field_v['placeholder'] : '' );
    $excluded = false;
    if ( isset( $wcdg_field_v['enable'] ) ) {
        $excluded = !empty( $wcdg_field_v['enable'] );
    }
    $disable_country = '';
    if ( class_exists( 'Automattic\\WooCommerce\\Blocks\\Utils\\CartCheckoutUtils' ) && method_exists( 'Automattic\\WooCommerce\\Blocks\\Utils\\CartCheckoutUtils', 'is_checkout_block_default' ) && Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default() ) {
        $disable_country = ( 'billing_country' === $wcdg_field_k ? 'disable-country-row' : '' );
    }
    ?>
                            <tr class="<?php 
    echo esc_attr( $disable_country );
    ?>">
                                <?php 
    ?>
                                <td class="td_select">
                                    <input type="hidden" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][enable]" value="" />
                                    <input type="checkbox" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][enable]" <?php 
    echo ( $excluded ? 'checked=checked' : '' );
    ?> />
                                    <?php 
    ?>
                                </td>
                                <td class="td_name"><?php 
    echo esc_html( $wcdg_field_k );
    ?></td>
                                <td class="td_label"><input type="text" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][label]" value="<?php 
    echo esc_attr( $label );
    ?>" <?php 
    echo ( !isset( $wcdg_default_fields[$wcdg_field_k] ) ? ' disabled="disabled"' : '' );
    ?> /></td>
                                <td class="td_class"><input type="text" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][class]" value="<?php 
    echo esc_attr( $class );
    ?>" <?php 
    echo ( !isset( $wcdg_default_fields[$wcdg_field_k] ) ? ' disabled="disabled"' : '' );
    ?> /></td>
                                <td class="td_placeholder"><input type="text" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][placeholder]" value="<?php 
    echo esc_attr( $placeholder );
    ?>" <?php 
    echo ( !isset( $wcdg_default_fields[$wcdg_field_k] ) ? ' disabled="disabled"' : '' );
    ?> /></td>
                                <td class="td_enabled status"><span class="dashicons <?php 
    echo ( $excluded ? 'dashicons-yes-alt' : 'dashicons-dismiss' );
    ?>"></span></td>
                            </tr>
                            <?php 
}
?>
                    </tbody>
                </table>
            </div>
            <div class="product_header_title">
                <h2 style="margin-top:30px"><?php 
esc_html_e( 'Configuration', 'woo-checkout-for-digital-goods' );
?></h2>
            </div>
            <table class="form-table wcdg-table-outer wcdg-table-tooltip table-outer">
                <tbody>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Status', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Enable or disable the plugin\'s functionality. When enabled, selected checkout billing fields will be removed, and other settings will be set up.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
                        </th>
                        <td class="forminp">
                            <label class="switch">
                                <input type="checkbox" name="wcdg_status" value="on" <?php 
echo esc_attr( $wcdg_status );
?>>
                                <div class="slider round"></div>
                            </label>
                        </td>
                    </tr>
                    <?php 
?>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="perfect_match_title">
                                    <?php 
esc_html_e( 'Select User Role', 'woo-checkout-for-digital-goods' );
?>
                                    <span class="wcdg-pro-label"></span>
                                    <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Choose the user roles for which the plugin\'s functionality should be enabled.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                                </label>
                            </th>
                            <td class="forminp">
                                <select name="wcdg_user_role_field[]" id="wcdg_user_role_field" disabled  style="max-width: 350px;width: 100%;">
                                    <option value="in_pro"><?php 
esc_html_e( 'Select a user role', 'woo-checkout-for-digital-goods' );
?></option>
                                </select>
                            </td>
                        </tr>
                        <?php 
?>
                    
                    
                    <tr valign="top" class="wcdg_chk_btn_label_row wcdg-inner-setting">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Button Label', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Customize the label text for the "Quick Checkout" button.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                            </label>
                        </th>
                        <td class="forminp">
                            <label>
                                <input type="text" name="wcdg_chk_btn_label" value="<?php 
echo esc_attr( $wcdg_chk_btn_label );
?>" placeholder="<?php 
esc_attr_e( 'Enter Button Label', 'woo-checkout-for-digital-goods' );
?>">
                            </label>
                        </td>
                    </tr>
                    <?php 
?>
                        <tr valign="top" class="wcdg_chk_btn_label_row wcdg-inner-setting">
                            <th class="titledesc" scope="row">
                                <label for="perfect_match_title"><?php 
esc_html_e( 'Quick Checkout On', 'woo-checkout-for-digital-goods' );
?></label>
                            </th>
                            <td class="forminp">
                                <input type="radio" name="wcdg_chk_on" value="wcdg_down_virtual" <?php 
checked( $wcdg_chk_on, 'wcdg_down_virtual' );
?> > <?php 
esc_html_e( 'Apply quick checkout to all downloadable and/or virtual products', 'woo-checkout-for-digital-goods' );
?><br>
                                <input disabled="disabled" type="radio" name="" value="" class="wcdg_read_only"> <?php 
esc_html_e( 'To manually select products, categories, or tags, ', 'woo-checkout-for-digital-goods' );
?> <span class="wcdg-pro-label"></span>
                            </td>
                        </tr>
                        <?php 
?>
                    <tr valign="top" class="wcdg_cart_btn_label_row wcdg-inner-setting">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Button Label', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Set a custom label for the "Add to Cart" button.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                            </label>
                        </th>
                        <td class="forminp">
                            <label>
                                <input type="text" name="wcdg_cart_btn_label" value="<?php 
echo esc_attr( $wcdg_cart_btn_label );
?>" placeholder="<?php 
esc_attr_e( 'Enter Button Label', 'woo-checkout-for-digital-goods' );
?>">
                            </label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Exclude Order Note', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Check this option to remove the optional "Order Notes" field from the checkout page.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
                        </th>
                        <td class="forminp">
                            <label>
                                <input type="checkbox" name="wcdg_chk_order_note" value="on" <?php 
echo esc_attr( $wcdg_chk_order_note );
?>>
                            </label>
                        </td>
                    </tr>
                    <?php 
?>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="perfect_match_title">
                                    <?php 
esc_html_e( 'Update on Thank You Page', 'woo-checkout-for-digital-goods' );
?>
                                    <span class="wcdg-pro-label"></span>
                                    <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Display a button on the Thank You page to allow users to update their information.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                </label>
                            </th>
                            <td class="forminp">
                                <label>
                                    <input type="checkbox" name="wcdg_allow_additional_field_update_flag" disabled value="on" class="wcdg_read_only">
                                </label>
                            </td>
                        </tr>
                        <?php 
?>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="submit_setting" class="button button-primary button-large" value="<?php 
echo esc_attr( 'Save', 'woo-checkout-for-digital-goods' );
?>">
                <input type="submit" name="reset_master_settings" class="button button-secondary" value="<?php 
esc_attr_e( 'Reset to Default', 'woo-checkout-for-digital-goods' );
?>" onclick="return confirm('Are you sure you want to reset plugin settings to default?');" style="float:right;"></p>
            </p>
        </form>
    </div>
    <div id="wcdg-add-field-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; padding:30px; width:500px; max-width:95vw; margin:100px auto; border-radius:8px; position:relative; max-height:80vh; overflow-y:auto;">
            <form id="wcdg-add-field-form">
                <h2 style="margin-top:0;"><?php 
esc_html_e( 'Add New Billing Field', 'woo-checkout-for-digital-goods' );
?></h2>
                <p>
                    <label><?php 
esc_html_e( 'Field Label', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <input type="text" name="label" id="wcdg-field-label" required class="regular-text">
                </p>
                <p>
                    <label><?php 
esc_html_e( 'Field Name', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <input type="text" name="name" id="wcdg-field-name" required class="regular-text" readonly>
                </p>
                <p>
                    <label><?php 
esc_html_e( 'Type', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <select name="type" id="wcdg-field-type" class="regular-text">
                        <option value="text"><?php 
echo esc_html__( 'Text', 'woo-checkout-for-digital-goods' );
?></option>
                        <option value="email"><?php 
echo esc_html__( 'Email', 'woo-checkout-for-digital-goods' );
?></option>
                        <option value="checkbox"><?php 
echo esc_html__( 'Checkbox', 'woo-checkout-for-digital-goods' );
?></option>
                        <option value="textarea"><?php 
echo esc_html__( 'Textarea', 'woo-checkout-for-digital-goods' );
?></option>
                        <option value="select"><?php 
echo esc_html__( 'Select', 'woo-checkout-for-digital-goods' );
?></option>
                    </select>
                </p>
                <p>
                    <label><?php 
esc_html_e( 'Class', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <input type="text" name="class" id="wcdg-field-class" class="regular-text">
                </p>
                <p>
                    <label><?php 
esc_html_e( 'Placeholder', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <input type="text" name="placeholder" id="wcdg-field-placeholder" class="regular-text">
                </p>
                <p>
                    <label><input type="checkbox" name="required" id="wcdg-field-required" value="1"> <?php 
esc_html_e( 'Required', 'woo-checkout-for-digital-goods' );
?></label>
                </p>
                <p>
                    <label><?php 
esc_html_e( 'Default Value', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <input type="text" name="default" id="wcdg-field-default" class="regular-text">
                </p>
                <p>
                    <label><input type="checkbox" name="show_in_email" id="wcdg-field-show-in-email" value="1"> <?php 
esc_html_e( 'Display in Emails', 'woo-checkout-for-digital-goods' );
?></label>
                </p>
                <p>
                    <label><input type="checkbox" name="show_in_order" id="wcdg-field-show-in-order" value="1"> <?php 
esc_html_e( 'Display in Order Detail Pages', 'woo-checkout-for-digital-goods' );
?></label>
                </p>
                <p id="wcdg-select-options-wrapper" style="display:none;">
                    <label><?php 
esc_html_e( 'Select Options', 'woo-checkout-for-digital-goods' );
?></label><br>
                    <div id="wcdg-select-options-list"></div>
                    <button type="button" class="button" id="wcdg-add-select-option">Add Option</button>
                </p>
                <input type="hidden" id="wcdg-edit-mode" value="">
                <button type="button" class="button button-danger" id="wcdg-delete-field" style="display:none;float:right;">Delete</button>
                <p style="margin-top:20px;">
                    <button type="submit" class="button button-primary" id="wcdg-add-save-field-btn">Add Field</button>
                    <button type="button" class="button" id="wcdg-cancel-add-field"><?php 
esc_html_e( 'Cancel', 'woo-checkout-for-digital-goods' );
?></button>
                </p>
            </form>
        </div>
    </div>
<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';