<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
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
$wcdg_chk_on = ( isset( $wcdg_general_setting['wcdg_chk_on'] ) && !empty( $wcdg_general_setting['wcdg_chk_on'] ) ? $wcdg_general_setting['wcdg_chk_on'] : 'wcdg_down_virtual' );
$wcdg_user_role_field = ( isset( $wcdg_general_setting['wcdg_user_role_field'] ) && !empty( $wcdg_general_setting['wcdg_user_role_field'] ) ? $wcdg_general_setting['wcdg_user_role_field'] : '' );
$wcdg_allow_additional_field_update_flag = ( isset( $wcdg_general_setting['wcdg_allow_additional_field_update_flag'] ) && !empty( $wcdg_general_setting['wcdg_allow_additional_field_update_flag'] ) ? 'checked' : '' );
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
    ?>
                            <tr>
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
esc_html_e( 'Enable / Disable', 'woo-checkout-for-digital-goods' );
?>
                            <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Enable or Disable functionality of plugin', 'woo-checkout-for-digital-goods' ) ), array(
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
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="perfect_match_title">
                            <?php 
esc_html_e( 'Exclude order note', 'woo-checkout-for-digital-goods' );
?>
                            <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Remove order note from checkout page.', 'woo-checkout-for-digital-goods' ) ), array(
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
                <tr valign="top">
                    <th class="titledesc" scope="row">
                        <label for="perfect_match_title">
                            <?php 
esc_html_e( 'Quick checkout for shop page', 'woo-checkout-for-digital-goods' );
?>
                            <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Display Quick Checkout Button on Shop Page for Digital Product', 'woo-checkout-for-digital-goods' ) ), array(
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
esc_html_e( 'Quick checkout for detail page', 'woo-checkout-for-digital-goods' );
?>
                            <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Display Quick Checkout Button on Product Details Page for Digital Product', 'woo-checkout-for-digital-goods' ) ), array(
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
                <?php 
?>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title"><?php 
esc_html_e( 'Quick checkout on', 'woo-checkout-for-digital-goods' );
?></label>
                        </th>
                        <td class="forminp">
                            <input type="radio" name="wcdg_chk_on" value="wcdg_down_virtual" <?php 
checked( $wcdg_chk_on, 'wcdg_down_virtual' );
?>> <?php 
esc_html_e( 'Quick Checkout for all downloadable and/or virtual products', 'woo-checkout-for-digital-goods' );
?><br>
                            <input disabled="disabled" type="radio" name="" value="" class="wcdg_read_only"> <?php 
esc_html_e( 'Manually Quick Checkout List for Product/Category/Tag ', 'woo-checkout-for-digital-goods' );
?> <span class="wcdg-pro-label"></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="perfect_match_title">
                                <?php 
esc_html_e( 'Select user role', 'woo-checkout-for-digital-goods' );
?>
                                <span class="wcdg-pro-label"></span>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Select user role which you want to enable this plugin.', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                            </label>
                        </th>
                        <td class="forminp">
                            <select name="wcdg_user_role_field[]" id="wcdg_user_role_field" disabled style="max-width: 350px;width: 100%;">
                                <option value="in_pro"><?php 
esc_html_e( 'Select a user role', 'woo-checkout-for-digital-goods' );
?></option>
                            </select>
                        </td>
                    </tr>
                <?php 
?>
                <?php 
?>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="perfect_match_title">
                                    <?php 
esc_html_e( 'Update on thank you page', 'woo-checkout-for-digital-goods' );
?>
                                    <span class="wcdg-pro-label"></span>
                                    <?php 
echo wp_kses( wc_help_tip( esc_html__( 'It will show button for the update user information on thank you page', 'woo-checkout-for-digital-goods' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>        
                                </label>
                            </th>
                            <td class="forminp">
                                <label>
                                    <input type="checkbox" name="wcdg_allow_additional_field_update_flag" disabled value="on" <?php 
echo esc_attr( $wcdg_allow_additional_field_update_flag );
?> class="wcdg_read_only">
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