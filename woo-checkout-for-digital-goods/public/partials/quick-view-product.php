<?php
/**
 * Quick View modal body (loaded via Ajax).
 *
 * @package Woo_Checkout_For_Digital_Goods
 *
 * @var WC_Product $product Product object.
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $product ) || ! $product instanceof WC_Product ) {
	return;
}

$heading_id = 'wcdg-qv-title-' . (int) $product->get_id();
$price_html = apply_filters( 'woocommerce_get_price_html', $product->get_price_html(), $product );
?>
<div class="wcdg-qv-grid">
	<div class="wcdg-qv-col wcdg-qv-col-image">
		<?php echo wp_kses_post( apply_filters( 'wcdg_quick_view_product_image', $product->get_image( 'woocommerce_single' ), $product ) ); ?>
	</div>
	<div class="wcdg-qv-col wcdg-qv-col-summary">
		<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="wcdg-qv-product-title product_title entry-title"><?php echo esc_html( $product->get_name() ); ?></h2>
		<?php if ( $price_html ) : ?>
			<div class="wcdg-qv-price-wrap">
				<p class="price wcdg-qv-price"><?php echo wp_kses_post( $price_html ); ?></p>
			</div>
		<?php endif; ?>
		<?php if ( $product->get_short_description() ) : ?>
			<div class="wcdg-qv-excerpt woocommerce-product-details__short-description">
				<?php echo wp_kses_post( apply_filters( 'woocommerce_short_description', $product->get_short_description() ) ); ?>
			</div>
		<?php endif; ?>
		<div class="wcdg-qv-add-to-cart">
			<?php woocommerce_template_single_add_to_cart(); ?>
		</div>
		<?php
		$qv_quick_checkout = isset( $quick_checkout_html ) ? $quick_checkout_html : '';        
		$qv_quick_checkout = apply_filters( 'wcdg_quick_view_quick_checkout_html', $qv_quick_checkout, $product );
		if ( $qv_quick_checkout ) {
			echo '<div class="wcdg-qv-quick-checkout">' . $qv_quick_checkout . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		<?php
		$cat_list = wc_get_product_category_list( $product->get_id(), ', ' );
		$tag_list = wc_get_product_tag_list( $product->get_id(), ', ' );
		if ( $cat_list || $tag_list ) :
			?>
			<div class="wcdg-qv-bottom-meta">
				<?php if ( $cat_list ) : ?>
					<div class="wcdg-qv-meta wcdg-qv-categories">
						<span class="wcdg-qv-meta-label"><?php esc_html_e( 'Categories', 'woo-checkout-for-digital-goods' ); ?>:</span>
						<?php echo wp_kses_post( $cat_list ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $tag_list ) : ?>
					<div class="wcdg-qv-meta wcdg-qv-tags">
						<span class="wcdg-qv-meta-label"><?php esc_html_e( 'Tags', 'woo-checkout-for-digital-goods' ); ?>:</span>
						<?php echo wp_kses_post( $tag_list ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
