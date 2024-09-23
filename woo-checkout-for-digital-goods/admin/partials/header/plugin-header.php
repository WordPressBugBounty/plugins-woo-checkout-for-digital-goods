<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
global $wcfdg_fs;
$plugin_version = esc_html( 'v' . WCDG_PLUGIN_VERSION );
$version_label = 'Free';
$plugin_slug = 'basic_digital_goods';
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$general_setting = ( isset( $current_page ) && 'wcdg-general-setting' === $current_page ? 'active' : '' );
$quick_checkout = ( isset( $current_page ) && 'wcdg-quick-checkout' === $current_page ? 'active' : '' );
$wcdg_getting_started = ( isset( $current_page ) && 'wcdg-get-started' === $current_page ? 'active' : '' );
$wcdg_import_export = ( isset( $current_page ) && 'wcdg-import-export' === $current_page ? 'active' : '' );
$wcdg_account_page = ( isset( $current_page ) && 'wcdg-general-setting-account' === $current_page ? 'active' : '' );
$wcdg_settings_menu = ( isset( $current_page ) && 'wcdg-import-export' === $current_page ? 'active' : '' );
$wcdg_free_dashboard = ( isset( $current_page ) && 'wcdg-upgrade-dashboard' === $current_page ? 'active' : '' );
$wcdg_display_submenu = ( !empty( $wcdg_settings_menu ) && 'active' === $wcdg_settings_menu ? 'display:inline-block' : 'display:none' );
$admin_object = new Woo_Checkout_For_Digital_Goods_Admin('', '');
?>
<div id="dotsstoremain">
    <div class="all-pad">
        <hr class="wp-header-end" />
        <?php 
// Get Dynamic promotional bar
$admin_object->wcdg_get_promotional_bar( $plugin_slug );
?>
        <header class="dots-header">
            <div class="dots-plugin-details">
                <div class="dots-header-left">
                    <div class="dots-logo-main">
                        <img src="<?php 
echo esc_url( WCDG_PLUGIN_URL . 'admin/images/woo-digital-goods-checkout-icon.png' );
?>">
                    </div>
                    <div class="plugin-name">
                        <div class="title"><?php 
esc_html_e( 'Digital Goods For Checkout', 'woo-checkout-for-digital-goods' );
?></div>
                    </div>
                    <span class="version-label <?php 
echo esc_attr( $plugin_slug );
?>"><?php 
echo esc_html( $version_label );
?></span>
                    <span class="version-number"><?php 
echo esc_html( $plugin_version );
?></span>
                </div>
                <div class="dots-header-right">
                    <div class="button-dots">
                        <a target="_blank" href="<?php 
echo esc_url( 'http://www.thedotstore.com/support/' );
?>">
                            <?php 
esc_html_e( 'Support', 'woo-checkout-for-digital-goods' );
?>
                        </a>
                    </div>
                    <div class="button-dots">
                        <a target="_blank" href="<?php 
echo esc_url( 'https://www.thedotstore.com/feature-requests/' );
?>">
                            <?php 
esc_html_e( 'Suggest', 'woo-checkout-for-digital-goods' );
?>
                        </a>
                    </div>
                    <div class="button-dots <?php 
echo ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ? '' : 'last-link-button' );
?>">
                        <a target="_blank" href="<?php 
echo esc_url( 'https://docs.thedotstore.com/category/170-premium-plugin-settings' );
?>">
                            <?php 
esc_html_e( 'Help', 'woo-checkout-for-digital-goods' );
?>
                        </a>
                    </div>
                    <div class="button-dots">
                        <?php 
?>
                            <a class="dots-upgrade-btn" target="_blank" href="javascript:void(0);"><?php 
esc_html_e( 'Upgrade Now', 'woo-checkout-for-digital-goods' );
?></a>
                            <?php 
?>
                    </div>
                </div>
            </div>
            <div class="dots-bottom-menu-main">
                <div class="dots-menu-main">
                    <nav>
                        <ul>
                            <li>
                                <a class="dotstore_plugin <?php 
echo esc_attr( $general_setting );
?>" href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'wcdg-general-setting',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'General Setting', 'woo-checkout-for-digital-goods' );
?></a>
                            </li>
                            <li>
                                <a class="dotstore_plugin <?php 
echo esc_attr( $quick_checkout );
?>" href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'wcdg-quick-checkout',
    'tab'  => 'products',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Quick Checkout', 'woo-checkout-for-digital-goods' );
?></a>
                            </li>
                            <?php 
$get_settings_page_url = add_query_arg( array(
    'page' => 'wcdg-import-export',
), admin_url( 'admin.php' ) );
?>
                            <li>
                                <a class="dotstore_plugin <?php 
echo esc_attr( $wcdg_settings_menu );
?>" href="<?php 
echo esc_url( $get_settings_page_url );
?>"><?php 
esc_html_e( 'Settings', 'woo-checkout-for-digital-goods' );
?></a>
                            </li>
                            <?php 
if ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) {
    ?>
                                <li>
                                    <a class="dotstore_plugin <?php 
    echo esc_attr( $wcdg_account_page );
    ?>" href="<?php 
    echo esc_url( $wcfdg_fs->get_account_url() );
    ?>"><?php 
    esc_html_e( 'License', 'woo-checkout-for-digital-goods' );
    ?></a>
                                </li>
                                <?php 
} else {
    ?>
                                <li>
                                    <a class="dotstore_plugin dots_get_premium <?php 
    echo esc_attr( $wcdg_free_dashboard );
    ?>" href="<?php 
    echo esc_url( add_query_arg( array(
        'page' => 'wcdg-upgrade-dashboard',
    ), admin_url( 'admin.php' ) ) );
    ?>"><?php 
    esc_html_e( 'Get Premium', 'woo-checkout-for-digital-goods' );
    ?></a>
                                </li>
                                <?php 
}
?>
                        </ul>
                    </nav>
                </div>
                <div class="dots-getting-started">
                    <a href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'wcdg-get-started',
), admin_url( 'admin.php' ) ) );
?>" class="<?php 
echo esc_attr( $wcdg_getting_started );
?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z" fill="#a0a0a0"></path></svg></a>
                </div>
            </div>
        </header>
        <!-- Upgrade to pro popup -->
        <?php 
if ( !(wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code()) ) {
    require_once WCDG_PLUGIN_PATH . 'admin/partials/dots-upgrade-popup.php';
}
?>
        <div class="dots-settings-inner-main">
            <div class="dots-settings-left-side">
                <div class="dots-submenu-items" style="<?php 
echo esc_attr( $wcdg_display_submenu );
?>">
                    <ul>
                        <li><a class="<?php 
echo esc_attr( $wcdg_import_export );
?>" href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'wcdg-import-export',
), admin_url( 'admin.php' ) ) );
?>"><?php 
esc_html_e( 'Import / Export', 'woo-checkout-for-digital-goods' );
?></a></li>
                        <li><a href="<?php 
echo esc_url( 'https://www.thedotstore.com/plugins/' );
?>" target="_blank"><?php 
esc_html_e( 'Shop Plugins', 'woo-checkout-for-digital-goods' );
?></a></li>
                    </ul>
                </div>
                