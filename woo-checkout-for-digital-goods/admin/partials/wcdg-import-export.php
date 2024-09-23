<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php');
?>
<div class="wcdg-main-left-section <?php echo esc_attr( ! ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) ? 'wcdg-upgrade-pro-to-unlock' : '' ); ?>">
    <h2>
        <?php 
            esc_html_e('Import & Export Digital Goods Settings', 'woo-checkout-for-digital-goods');
            if ( ! ( wcfdg_fs()->is__premium_only() && wcfdg_fs()->can_use_premium_code() ) ) {
                ?><span class="wcdg-pro-label"></span><?php
            }
        ?>    
    </h2>
    <table class="form-table wcdg-table-outer table-outer">
        <tbody>
            <tr>
                <th class="titledesc" scope="row">
                    <label for="perfect_match_title"><?php esc_html_e('Export Digital Goods Settings', 'woo-checkout-for-digital-goods'); ?></label>                    
                </th>
                <td class="forminp">
                    <div class="wcdg_main_container export_settings_container">
                        <p class="wcdg_button_container">
                            <input type="button" name="wcdg_export_settings" id="wcdg_export_settings" class="button button-primary" value="<?php esc_attr_e( 'Export', 'woo-checkout-for-digital-goods' ); ?>" />
                        </p>
                        <p class="wcdg_content_container">
                            <?php wp_nonce_field( 'wcdg_export_save_action_nonce', 'wcdg_export_action_nonce' ); ?>
                            <input type="hidden" name="wcdg_export_action" value="wcdg_export_settings_action"/>
                            <span><?php esc_html_e( 'Export the digital goods general settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'woo-checkout-for-digital-goods' ); ?></span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row" class="titledesc">
                    <label for="blogname"><?php echo esc_html__( 'Import Digital Goods Settings', 'woo-checkout-for-digital-goods' ); ?></label>
                </th>
                <td>
                    <div class="wcdg_main_container import_settings_container">
                        <p class="wcdg_file_container">
                            <input type="file" name="import_file" accept="application/json" />
                        </p>
                        <p class="wcdg_button_container">
                            <input type="button" name="wcdg_import_settings" id="wcdg_import_settings" class="button button-primary" value="<?php esc_attr_e( 'Import', 'woo-checkout-for-digital-goods' ); ?>" />
                        </p>
                        <p class="wcdg_content_container">
                            <input type="hidden" name="wcdg_import_action" value="wcdg_import_settings_action"/>
                            <?php wp_nonce_field( 'wcdg_import_action_nonce', 'wcdg_import_action_nonce' ); ?>
                            <span><?php esc_html_e( 'Import the digital goods general settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'woo-checkout-for-digital-goods' ); ?></span>
                        </p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php 
require_once(plugin_dir_path( __FILE__ ).'header/plugin-footer.php');
