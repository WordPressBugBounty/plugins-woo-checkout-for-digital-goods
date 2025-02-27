<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php');
?>
<div class="wcdg-main-left-section res-cl">
    <div class="wcdg-main-table wcdg-getting-started res-cl">
        <div class="dots-getting-started-main">
            <div class="getting-started-content">
                <span><?php esc_html_e( 'How to Get Started', 'woo-checkout-for-digital-goods' ); ?></span>
                <h3><?php esc_html_e( 'Welcome to Digital Goods For Checkout Plugin', 'woo-checkout-for-digital-goods' ); ?></h3>
                <p><?php esc_html_e( 'Thank you for choosing our top-rated WooCommerce Digital Goods For Checkout plugin. Our user-friendly interface makes bypassing unnecessary fields easier to complete your digital order faster.', 'woo-checkout-for-digital-goods' ); ?></p>
                <p>
                    <?php 
                    echo sprintf(
                        esc_html__('To help you get started, watch the quick tour video on the right. For more help, explore our help documents or visit our %s for detailed video tutorials.', 'woo-checkout-for-digital-goods'),
                        '<a href="' . esc_url('https://www.youtube.com/@Dotstore16?sub_confirmation=1') . '" target="_blank">' . esc_html__('YouTube channel', 'woo-checkout-for-digital-goods') . '</a>',
                    );
                    ?>
                </p>
                <div class="getting-started-actions">
                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'wcdg-general-setting'), admin_url('admin.php'))); ?>" class="quick-start"><?php esc_html_e( 'Set Digital Goods Checkout', 'woo-checkout-for-digital-goods' ); ?><span class="dashicons dashicons-arrow-right-alt"></span></a>
                    <a href="https://docs.thedotstore.com/article/202-getting-started" target="_blank" class="setup-guide"><span class="dashicons dashicons-book-alt"></span><?php esc_html_e( 'Read the Setup Guide', 'woo-checkout-for-digital-goods' ); ?></a>
                </div>
            </div>
            <div class="getting-started-video">
                <iframe width="960" height="600" src="<?php echo esc_url('https://www.youtube.com/embed/s8JNmlwKm1Q'); ?>" title="<?php esc_attr_e( 'Plugin Tour', 'woo-checkout-for-digital-goods' ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
<?php 
require_once(plugin_dir_path( __FILE__ ).'header/plugin-footer.php'); 
