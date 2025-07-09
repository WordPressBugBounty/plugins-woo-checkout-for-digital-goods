<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap wcdg-quick-checkout-page"><h2></h2></div>
<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$wcdg_get_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$wcdg_get_action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
// Save logic for Master Settings.
if ( isset( $_POST['submit_master_settings'] ) ) {
    if ( !isset( $_POST['woo_checkout_digital_goods_master'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woo_checkout_digital_goods_master'] ) ), basename( __FILE__ ) ) ) {
        die( 'Failed security check' );
    }
    $general_setting_data = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
    $general_setting_data['wcdg_chk_prod'] = ( !empty( $_POST['wcdg_chk_prod'] ) ? sanitize_text_field( $_POST['wcdg_chk_prod'] ) : '' );
    $general_setting_data['wcdg_chk_details'] = ( !empty( $_POST['wcdg_chk_details'] ) ? sanitize_text_field( $_POST['wcdg_chk_details'] ) : '' );
    $general_setting_data['wcdg_chk_btn_label'] = ( !empty( $_POST['wcdg_chk_btn_label'] ) ? sanitize_text_field( $_POST['wcdg_chk_btn_label'] ) : '' );
    $general_setting_data['wcdg_chk_on'] = ( !empty( $_POST['wcdg_chk_on'] ) ? sanitize_text_field( $_POST['wcdg_chk_on'] ) : 'wcdg_down_virtual' );
    $general_setting_data['wcdg_enable_cart_btn_label'] = ( !empty( $_POST['wcdg_enable_cart_btn_label'] ) ? sanitize_text_field( $_POST['wcdg_enable_cart_btn_label'] ) : '' );
    $general_setting_data['wcdg_cart_btn_label'] = ( !empty( $_POST['wcdg_cart_btn_label'] ) ? sanitize_text_field( $_POST['wcdg_cart_btn_label'] ) : '' );
    update_option( 'wcdg_checkout_setting', $general_setting_data );
}
$wcdg_general_setting = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
$wcdg_chk_prod = ( isset( $wcdg_general_setting['wcdg_chk_prod'] ) && !empty( $wcdg_general_setting['wcdg_chk_prod'] ) ? 'checked' : '' );
$wcdg_chk_details = ( isset( $wcdg_general_setting['wcdg_chk_details'] ) && !empty( $wcdg_general_setting['wcdg_chk_details'] ) ? 'checked' : '' );
$wcdg_chk_btn_label = ( isset( $wcdg_general_setting['wcdg_chk_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_chk_btn_label'] ) ? $wcdg_general_setting['wcdg_chk_btn_label'] : '' );
$wcdg_chk_on = ( isset( $wcdg_general_setting['wcdg_chk_on'] ) && !empty( $wcdg_general_setting['wcdg_chk_on'] ) ? $wcdg_general_setting['wcdg_chk_on'] : 'wcdg_down_virtual' );
$wcdg_enable_cart_btn_label = ( isset( $wcdg_general_setting['wcdg_enable_cart_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_enable_cart_btn_label'] ) ? 'checked' : '' );
$wcdg_cart_btn_label = ( isset( $wcdg_general_setting['wcdg_cart_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_cart_btn_label'] ) ? $wcdg_general_setting['wcdg_cart_btn_label'] : '' );
// Function for free plugin content
function wcdg_free_quick_checkout_settings_content() {
    $wcdg_get_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $wcdg_general_setting = maybe_unserialize( get_option( 'wcdg_checkout_setting' ) );
    $wcdg_chk_prod = ( isset( $wcdg_general_setting['wcdg_chk_prod'] ) && !empty( $wcdg_general_setting['wcdg_chk_prod'] ) ? 'checked' : '' );
    $wcdg_chk_details = ( isset( $wcdg_general_setting['wcdg_chk_details'] ) && !empty( $wcdg_general_setting['wcdg_chk_details'] ) ? 'checked' : '' );
    $wcdg_chk_btn_label = ( isset( $wcdg_general_setting['wcdg_chk_btn_label'] ) && !empty( $wcdg_general_setting['wcdg_chk_btn_label'] ) ? $wcdg_general_setting['wcdg_chk_btn_label'] : '' );
    $wcdg_chk_on = ( isset( $wcdg_general_setting['wcdg_chk_on'] ) && !empty( $wcdg_general_setting['wcdg_chk_on'] ) ? $wcdg_general_setting['wcdg_chk_on'] : 'wcdg_down_virtual' );
    ?>
    <div class="wcdg-main-left-section res-cl">
            <div class="product_header_title">
                <h2><?php 
    esc_html_e( 'Button Settings', 'woo-checkout-for-digital-goods' );
    ?>
                    <span class="woocommerce-help-tip" data-tip="<?php 
    echo esc_attr__( 'Quick checkout button settings', 'woo-checkout-for-digital-goods' );
    ?>"></span>
                </h2>
            </div>
            <form method="post" action="">
                <?php 
    wp_nonce_field( basename( __FILE__ ), 'woo_checkout_digital_goods_master' );
    ?>
                <table class="form-table wcdg-table-outer wcdg-table-tooltip table-outer">
                    <tbody>
                        <tr valign="top">
                            <th class="titledesc" scope="row">
                                <label for="perfect_match_title">
                                    <?php 
    esc_html_e( 'Enable on Shop/Archive Page', 'woo-checkout-for-digital-goods' );
    ?>
                                    <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'Display a "Quick Checkout" button on the shop page for digital products.', 'woo-checkout-for-digital-goods' ) ), array(
        'span' => array(
            'class'      => true,
            'data-tip'   => true,
            'aria-label' => true,
        ),
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
    esc_html_e( 'Enable on Product Page', 'woo-checkout-for-digital-goods' );
    ?>
                                    <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'Show a "Quick Checkout" button on the product details page for digital products.', 'woo-checkout-for-digital-goods' ) ), array(
        'span' => array(
            'class'      => true,
            'data-tip'   => true,
            'aria-label' => true,
        ),
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
        'span' => array(
            'class'      => true,
            'data-tip'   => true,
            'aria-label' => true,
        ),
    ) );
    ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <label>
                                    <input type="text" name="wcdg_chk_btn_label" value="<?php 
    echo esc_attr( $wcdg_chk_btn_label );
    ?>" placeholder="<?php 
    esc_attr_e( 'Quick Checkout', 'woo-checkout-for-digital-goods' );
    ?>">
                                </label>
                            </td>
                        </tr>
                        <tr valign="top" class="wcdg_chk_btn_label_row wcdg-inner-setting">
                            <th class="titledesc" scope="row">
                                <label for="perfect_match_title"><?php 
    esc_html_e( 'Default Enable On', 'woo-checkout-for-digital-goods' );
    ?></label>
                            </th>
                            <td class="forminp">
                                <input type="radio" name="wcdg_chk_on" value="wcdg_down_virtual" <?php 
    checked( $wcdg_chk_on, 'wcdg_down_virtual' );
    ?>> <?php 
    esc_html_e( 'To all downloadable and/or virtual products', 'woo-checkout-for-digital-goods' );
    ?><br>
                                <input disabled="disabled" type="radio" name="" value="" class="wcdg_read_only"> <?php 
    esc_html_e( 'To manually select products, categories, or tags ', 'woo-checkout-for-digital-goods' );
    ?> <span class="wcdg-pro-label"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="submit_master_settings" class="button button-primary button-large" value="<?php 
    echo esc_attr( 'Save', 'woo-checkout-for-digital-goods' );
    ?>">
            </form>
        
        <div class="wcdg-upgrade-pro-to-unlock">
            <div class="product_header_title">
                <h2><?php 
    esc_html_e( 'Product Selection', 'woo-checkout-for-digital-goods' );
    ?>
                    <span class="woocommerce-help-tip" data-tip="<?php 
    echo esc_attr__( 'The selected products, categories, or tags will display the Quick Checkout button on the product and listing pages. This feature does not affect or customize the checkout fields. It simply enables the quick checkout option for the selected items.', 'woo-checkout-for-digital-goods' );
    ?>"></span>
                    <span class="wcdg-pro-label"></span>
                </h2>
            </div>        
            <div class="wcdg-data-container">
                <ul class="wcdg-tab">
                    <li><a class="pvcp-action-link active" href="#"><?php 
    esc_html_e( 'Products', 'woo-checkout-for-digital-goods' );
    ?></a></li>
                    <li><a class="pvcp-action-link " href="#"><?php 
    esc_html_e( 'Categories', 'woo-checkout-for-digital-goods' );
    ?></a></li>
                    <li><a class="pvcp-action-link " href="#"><?php 
    esc_html_e( 'Tags', 'woo-checkout-for-digital-goods' );
    ?></a></li>
                </ul>
                <div class="ds-wrap">
                    <form method="post" action="" class="wcdg_chk_form wcdg_product_form">
                        <select name="wcdg_chk_product[]" id="wcdg-chk-product-filter" class="multiselect2" data-allow_clear="true" data-placeholder="<?php 
    esc_attr_e( 'Select a product', 'woo-checkout-for-digital-goods' );
    ?>" data-minimum_input_length="3" >
                            <option value="in_pro"><?php 
    esc_html_e( 'Select a product', 'woo-checkout-for-digital-goods' );
    ?></option>
                        </select>
                        <div class="group-button">
                            <p><input type="submit" name="wcdg_submit_product" class="button button-primary button-large" value="<?php 
    echo esc_attr( 'Save Product', 'woo-checkout-for-digital-goods' );
    ?>" /></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php 
}

wcdg_free_quick_checkout_settings_content();
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';