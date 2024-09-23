<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$plugin_name = WCDG_PLUGIN_NAME;
$plugin_version = WCDG_PLUGIN_VERSION;
$version_text = '';
require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php');
if ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) {
    $version_text = esc_html( 'Pro Version', 'woo-checkout-for-digital-goods' );
} else {
    $version_text = esc_html( 'Free Version', 'woo-checkout-for-digital-goods' );
}
?>

<div class="wcdg-main-left-section">
    <div class="wcdg-main-table res-cl">
        <h2><?php esc_html_e('Quick info', 'woo-checkout-for-digital-goods'); ?></h2>
        <table class="table-outer">
            <tbody>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Product Type', 'woo-checkout-for-digital-goods'); ?></td>
                    <td class="fr-2"><?php esc_html_e('WooCommerce Plugin', 'woo-checkout-for-digital-goods'); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Product Name', 'woo-checkout-for-digital-goods'); ?></td>
                    <td class="fr-2"><?php echo esc_html( $plugin_name ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Installed Version', 'woo-checkout-for-digital-goods'); ?></td>
                    <td class="fr-2"><?php echo esc_html( $version_text ); ?> <?php echo esc_html( $plugin_version ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('License & Terms of use', 'woo-checkout-for-digital-goods'); ?></td>
                    <td class="fr-2"><a target="_blank"  href="<?php echo esc_url('https://www.thedotstore.com/terms-and-conditions/'); ?>"><?php esc_html_e('Click here', 'woo-checkout-for-digital-goods'); ?></a><?php esc_html_e(' to view license and terms of use.', 'woo-checkout-for-digital-goods'); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Help & Support', 'woo-checkout-for-digital-goods'); ?></td>
                    <td class="fr-2">
                        <ul>
                            <li><a href="<?php echo esc_url(admin_url('/admin.php?page=wcdg-get-started')); ?>"><?php esc_html_e('Quick Start', 'woo-checkout-for-digital-goods'); ?></a></li>
                            <li><a target="_blank" href="<?php echo esc_url('https://docs.thedotstore.com/category/170-premium-plugin-settings'); ?>"><?php esc_html_e('Guide Documentation', 'woo-checkout-for-digital-goods'); ?></a></li> 
                            <li><a target="_blank" href="<?php echo esc_url('https://www.thedotstore.com/support/'); ?>"><?php esc_html_e('Support Forum', 'woo-checkout-for-digital-goods'); ?></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Localization', 'woo-checkout-for-digital-goods'); ?></td>
                    <td class="fr-2"><?php esc_html_e('German, Spain, Polish, French', 'woo-checkout-for-digital-goods'); ?></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
<?php 
require_once(plugin_dir_path( __FILE__ ).'header/plugin-footer.php');
