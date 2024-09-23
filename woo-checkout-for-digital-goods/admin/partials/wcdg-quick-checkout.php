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
// Function for free plugin content
function wcdg_free_quick_checkout_settings_content() {
    $wcdg_get_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    ?>
    <div class="wcdg-main-left-section res-cl wcdg-upgrade-pro-to-unlock">
        <div class="product_header_title">
            <h2><?php 
    esc_html_e( 'Settings', 'woo-checkout-for-digital-goods' );
    ?><span class="wcdg-pro-label"></span></h2>
        </div>
        <?php 
    $product_type = ( isset( $wcdg_get_tab ) && 'products' === $wcdg_get_tab ? 'active' : '' );
    $cat_type = ( isset( $wcdg_get_tab ) && 'categories' === $wcdg_get_tab ? 'active' : '' );
    $tag_type = ( isset( $wcdg_get_tab ) && 'tags' === $wcdg_get_tab ? 'active' : '' );
    ?>
        <div class="wcdg-data-container">
            <ul class="wcdg-tab">
                <li><a class="pvcp-action-link <?php 
    echo esc_attr( $product_type );
    ?>" href="<?php 
    echo esc_url( site_url( '/wp-admin/admin.php?page=wcdg-quick-checkout&tab=products' ) );
    ?>"><?php 
    esc_html_e( 'Products', 'woo-checkout-for-digital-goods' );
    ?></a></li>
                <li><a class="pvcp-action-link <?php 
    echo esc_attr( $cat_type );
    ?>" href="<?php 
    echo esc_url( site_url( '/wp-admin/admin.php?page=wcdg-quick-checkout&tab=categories' ) );
    ?>"><?php 
    esc_html_e( 'Categories', 'woo-checkout-for-digital-goods' );
    ?></a></li>
                <li><a class="pvcp-action-link <?php 
    echo esc_attr( $tag_type );
    ?>" href="<?php 
    echo esc_url( site_url( '/wp-admin/admin.php?page=wcdg-quick-checkout&tab=tags' ) );
    ?>"><?php 
    esc_html_e( 'Tags', 'woo-checkout-for-digital-goods' );
    ?></a></li>
            </ul>
            <?php 
    if ( empty( $wcdg_get_tab ) || 'products' === $wcdg_get_tab ) {
        ?>
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
            <?php 
    } elseif ( 'categories' === $wcdg_get_tab ) {
        ?>
                <div class="ds-wrap">
                    <form method="post" action="" class="wcdg_chk_form wcdg_category_form">
                        <select id="wcdg-chk-category-filter" name="wcdg_chk_category[]" class="multiselect2">
                            <option value="in_pro"><?php 
        esc_html_e( 'Select a category', 'woo-checkout-for-digital-goods' );
        ?></option>
                        </select>
                        <div class="group-button">
                            <p><input type="submit" name="wcdg_submit_category" class="button button-primary button-large" value="<?php 
        echo esc_attr( 'Save Category', 'woo-checkout-for-digital-goods' );
        ?>"></p>
                        </div>
                        </tbody>
                    </table>
                </div>
            <?php 
    } elseif ( 'tags' === $wcdg_get_tab ) {
        ?>
                <div class="ds-wrap">
                    <form method="post" action="" class="wcdg_chk_form wcdg_tag_form">
                        <select id="wcdg-chk-tag-filter" name="wcdg_chk_tag[]" class="multiselect2">
                            <option value="in_pro"><?php 
        esc_html_e( 'Select a tag', 'woo-checkout-for-digital-goods' );
        ?></option>
                        </select>
                        <div class="group-button">
                            <p><input type="submit" name="wcdg_submit_tag" class="button button-primary button-large" value="<?php 
        echo esc_attr( 'Save Tag', 'woo-checkout-for-digital-goods' );
        ?>"></p>
                        </div>
                    </form> 
                </div>
            <?php 
    }
    ?>
        </div>
    </div>
    <?php 
}

wcdg_free_quick_checkout_settings_content();
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';