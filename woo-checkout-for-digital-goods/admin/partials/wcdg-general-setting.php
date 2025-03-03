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
        $general_setting_data = array();
        $get_wcdg_status = filter_input( INPUT_POST, 'wcdg_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_field = filter_input(
            INPUT_POST,
            'wcdg_chk_field',
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $get_wcdg_chk_order_note = filter_input( INPUT_POST, 'wcdg_chk_order_note', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_prod = filter_input( INPUT_POST, 'wcdg_chk_prod', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_details = filter_input( INPUT_POST, 'wcdg_chk_details', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_btn_label = filter_input( INPUT_POST, 'wcdg_chk_btn_label', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_enable_cart_btn_label = filter_input( INPUT_POST, 'wcdg_enable_cart_btn_label', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_cart_btn_label = filter_input( INPUT_POST, 'wcdg_cart_btn_label', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_chk_on = filter_input( INPUT_POST, 'wcdg_chk_on', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_wcdg_user_role_field = filter_input(
            INPUT_POST,
            'wcdg_user_role_field',
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $get_wcdg_allow_additional_field_update_flag = filter_input( INPUT_POST, 'wcdg_allow_additional_field_update_flag', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
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
        $general_setting_data['wcdg_chk_field'] = sanitize_array( $get_wcdg_chk_field );
        $general_setting_data['wcdg_chk_order_note'] = ( !empty( $get_wcdg_chk_order_note ) ? sanitize_text_field( $get_wcdg_chk_order_note ) : '' );
        $general_setting_data['wcdg_chk_prod'] = ( !empty( $get_wcdg_chk_prod ) ? sanitize_text_field( $get_wcdg_chk_prod ) : '' );
        $general_setting_data['wcdg_chk_details'] = ( !empty( $get_wcdg_chk_details ) ? sanitize_text_field( $get_wcdg_chk_details ) : '' );
        $general_setting_data['wcdg_chk_btn_label'] = ( !empty( $get_wcdg_chk_btn_label ) ? sanitize_text_field( $get_wcdg_chk_btn_label ) : '' );
        $general_setting_data['wcdg_enable_cart_btn_label'] = ( !empty( $get_wcdg_enable_cart_btn_label ) ? sanitize_text_field( $get_wcdg_enable_cart_btn_label ) : '' );
        $general_setting_data['wcdg_cart_btn_label'] = ( !empty( $get_wcdg_cart_btn_label ) ? sanitize_text_field( $get_wcdg_cart_btn_label ) : '' );
        $general_setting_data['wcdg_chk_on'] = ( !empty( $get_wcdg_chk_on ) ? sanitize_text_field( $get_wcdg_chk_on ) : 'wcdg_down_virtual' );
        $general_setting_data['wcdg_user_role_field'] = ( !empty( $get_wcdg_user_role_field ) ? array_map( 'sanitize_text_field', $get_wcdg_user_role_field ) : '' );
        $general_setting_data['wcdg_allow_additional_field_update_flag'] = ( !empty( $get_wcdg_allow_additional_field_update_flag ) ? sanitize_text_field( $get_wcdg_allow_additional_field_update_flag ) : '' );
        update_option( 'wcdg_checkout_setting', $general_setting_data );
    }
}
$wcdg_general_setting = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
$wcdg_status = ( isset( $wcdg_general_setting['wcdg_status'] ) && !empty( $wcdg_general_setting['wcdg_status'] ) ? 'checked' : '' );
$wcdg_ch_field = ( isset( $wcdg_general_setting['wcdg_chk_field'] ) && !empty( $wcdg_general_setting['wcdg_chk_field'] ) ? $wcdg_general_setting['wcdg_chk_field'] : array() );
$wcdg_chk_order_note = ( isset( $wcdg_general_setting['wcdg_chk_order_note'] ) && !empty( $wcdg_general_setting['wcdg_chk_order_note'] ) ? 'checked' : '' );
$wcdg_chk_prod = ( isset( $wcdg_general_setting['wcdg_chk_prod'] ) && !empty( $wcdg_general_setting['wcdg_chk_prod'] ) ? 'checked' : '' );
$wcdg_chk_details = ( isset( $wcdg_general_setting['wcdg_chk_details'] ) && !empty( $wcdg_general_setting['wcdg_chk_details'] ) ? 'checked' : '' );
$wcdg_chk_btn_label = ( isset( $wcdg_general_setting['wcdg_chk_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_chk_btn_label'] ) ? $wcdg_general_setting['wcdg_chk_btn_label'] : '' );
$wcdg_enable_cart_btn_label = ( isset( $wcdg_general_setting['wcdg_enable_cart_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_enable_cart_btn_label'] ) ? 'checked' : '' );
$wcdg_cart_btn_label = ( isset( $wcdg_general_setting['wcdg_cart_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_cart_btn_label'] ) ? $wcdg_general_setting['wcdg_cart_btn_label'] : '' );
$wcdg_chk_on = ( isset( $wcdg_general_setting['wcdg_chk_on'] ) && !empty( $wcdg_general_setting['wcdg_chk_on'] ) ? $wcdg_general_setting['wcdg_chk_on'] : 'wcdg_down_virtual' );
$wcdg_user_role_field = ( isset( $wcdg_general_setting['wcdg_user_role_field'] ) && !empty( $wcdg_general_setting['wcdg_user_role_field'] ) ? $wcdg_general_setting['wcdg_user_role_field'] : '' );
$wcdg_allow_additional_field_update_flag = ( isset( $wcdg_general_setting['wcdg_allow_additional_field_update_flag'] ) && !empty( $wcdg_general_setting['wcdg_allow_additional_field_update_flag'] ) ? 'checked' : '' );
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
                <?php 
    }
}
?>
        <form method="POST" name="" action="">
            <?php 
wp_nonce_field( basename( __FILE__ ), 'woo_checkout_digital_goods' );
?>
            <div class="wcdg-checkout-billing-fields">
                <h2><?php 
esc_html_e( 'Checkout Billing Fields', 'woo-checkout-for-digital-goods' );
?></h2>
                <table id="thwcfd_checkout_fields" class="wc_gateways widefat thpladmin_fields_table" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" /></th>
                            <th class="name"><?php 
esc_html_e( 'Name', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Label', 'woo-checkout-for-digital-goods' );
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
                            <th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" /></th>
                            <th class="name"><?php 
esc_html_e( 'Name', 'woo-checkout-for-digital-goods' );
?></th>
                            <th><?php 
esc_html_e( 'Label', 'woo-checkout-for-digital-goods' );
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
unset($wcdg_default_fields['billing_email']);
foreach ( $wcdg_default_fields as $wcdg_field_k => $wcdg_field_v ) {
    $label = ( isset( $wcdg_ch_field[$wcdg_field_k]['label'] ) && !empty( $wcdg_ch_field[$wcdg_field_k]['label'] ) ? $wcdg_ch_field[$wcdg_field_k]['label'] : $wcdg_field_v['label'] );
    $placeholder = ( isset( $wcdg_ch_field[$wcdg_field_k]['placeholder'] ) && !empty( $wcdg_ch_field[$wcdg_field_k]['placeholder'] ) ? $wcdg_ch_field[$wcdg_field_k]['placeholder'] : '' );
    $excluded = false;
    if ( is_array( $wcdg_ch_field ) ) {
        if ( wcdg_is_multi( $wcdg_ch_field ) ) {
            $excluded = ( isset( $wcdg_ch_field[$wcdg_field_k]['enable'] ) && !empty( $wcdg_ch_field[$wcdg_field_k]['enable'] ) ? $wcdg_ch_field[$wcdg_field_k]['enable'] : '' );
        } else {
            $excluded = in_array( $wcdg_field_k, $wcdg_ch_field, true );
        }
    }
    // Add disable class for country field with checkout block
    $disable_country = '';
    if ( class_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class ) && method_exists( Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class, 'is_checkout_block_default' ) && CartCheckoutUtils::is_checkout_block_default() ) {
        $disable_country = ( 'billing_country' === $wcdg_field_k ? 'disable-country-row' : '' );
    }
    ?>
                            <tr class="<?php 
    echo esc_attr( $disable_country );
    ?>">
                                <td class="td_select"><input type="hidden" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][enable]" value="" /><input type="checkbox" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][enable]" <?php 
    echo ( $excluded ? 'checked=checked' : '' );
    ?> /></td>
                                <td class="td_name"><?php 
    echo esc_html( $wcdg_field_k );
    ?></td>
                                <td class="td_label"><input type="text" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][label]" value="<?php 
    echo esc_attr( $label );
    ?>" /></td>
                                <td class="td_placeholder"><input type="text" name="wcdg_chk_field[<?php 
    echo esc_attr( $wcdg_field_k );
    ?>][placeholder]" value="<?php 
    echo esc_attr( $placeholder );
    ?>" /></td>
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
echo wp_kses( wc_help_tip( esc_html__( 'Enable or disable the plugin’s functionality. When enabled, selected checkout billing fields will be removed, and other settings will be set up.', 'woo-checkout-for-digital-goods' ) ), array(
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
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Quick Checkout on Shop Page', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Display a "Quick Checkout" button on the shop page for digital products.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
                        </th>
                        <td class="forminp">
                            <label >
                                <input type="checkbox" name="wcdg_chk_prod" value="on" <?php 
echo esc_attr( $wcdg_chk_prod );
?>>
                            </label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Quick Checkout on Product Page', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Show a "Quick Checkout" button on the product details page for digital products.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                            </label>
                        </th>
                        <td class="forminp">
                            <label>
                                <input type="checkbox" name="wcdg_chk_details" value="on" <?php 
echo esc_attr( $wcdg_chk_details );
?>>
                            </label>
                        </td>
                    </tr>
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
?>> <?php 
esc_html_e( 'Apply quick checkout to all downloadable and/or virtual products', 'woo-checkout-for-digital-goods' );
?><br>
                                <input disabled="disabled" type="radio" name="" value="" class="wcdg_read_only"> <?php 
esc_html_e( 'To manually select products, categories, or tags, ', 'woo-checkout-for-digital-goods' );
?> <span class="wcdg-pro-label"></span>
                            </td>
                        </tr>
                        <?php 
?>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Add to Cart Button Label', 'woo-checkout-for-digital-goods' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Enable this option to modify the default "Add to Cart" button label.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                            </label>
                        </th>
                        <td class="forminp">
                            <label>
                                <input type="checkbox" name="wcdg_enable_cart_btn_label" value="on" <?php 
echo esc_attr( $wcdg_enable_cart_btn_label );
?>>
                            </label>
                        </td>
                    </tr>
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
            <p class="submit"><input type="submit" name="submit_setting" class="button button-primary button-large" value="<?php 
echo esc_attr( 'Save', 'woo-checkout-for-digital-goods' );
?>"></p>
        </form>
    </div>
<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';