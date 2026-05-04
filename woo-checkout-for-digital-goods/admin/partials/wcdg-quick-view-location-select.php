<?php
/**
 * Quick View — multiselect location field (General settings).
 *
 * Options are grouped as Pages, product Categories, and product Tags only.
 *
 * @package Woo_Checkout_For_Digital_Goods
 *
 * @var array $wcdg_quick_view_pages Selected token values.
 */

defined( 'ABSPATH' ) || exit;

$selected = is_array( $wcdg_quick_view_pages ) ? $wcdg_quick_view_pages : array();

$opt = function ( $value, $label ) use ( $selected ) {
	printf(
		'<option value="%1$s" %2$s>%3$s</option>',
		esc_attr( $value ),
		selected( in_array( $value, $selected, true ), true, false ),
		esc_html( $label )
	);
};
?>
<select name="wcdg_quick_view_pages[]" id="wcdg_quick_view_pages" class="wcdg-quick-view-pages-select" multiple="multiple" data-placeholder="<?php echo esc_attr__( 'Select pages, categories, or tags…', 'woo-checkout-for-digital-goods' ); ?>">
	<optgroup label="<?php echo esc_attr__( 'Pages', 'woo-checkout-for-digital-goods' ); ?>">
		<?php
		$pages_list = get_pages(
			array(
				'post_status' => 'publish',
				'sort_column' => 'post_title',
				'sort_order'  => 'ASC',
				'number'      => 300,
			)
		);
		$shop_page_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
		if ( is_array( $pages_list ) ) {
			foreach ( $pages_list as $p ) {
				$label = $p->post_title;
				if ( $shop_page_id > 0 && (int) $p->ID === $shop_page_id ) {
					$label = sprintf(
						/* translators: %s: page title */
						__( '%s (WooCommerce shop)', 'woo-checkout-for-digital-goods' ),
						$p->post_title
					);
				}
				$opt( 'page_' . (int) $p->ID, $label );
			}
		}
		?>
	</optgroup>
	<?php if ( taxonomy_exists( 'product_cat' ) ) : ?>
		<optgroup label="<?php echo esc_attr__( 'Category', 'woo-checkout-for-digital-goods' ); ?>">
			<?php
			$cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 500,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
			if ( ! is_wp_error( $cats ) && is_array( $cats ) ) {
				foreach ( $cats as $cat_term ) {
                    $opt( 'cat_' . (int) $cat_term->term_id, $cat_term->name );
                }
			}
			?>
		</optgroup>
	<?php endif; ?>
	<?php if ( taxonomy_exists( 'product_tag' ) ) : ?>
		<optgroup label="<?php echo esc_attr__( 'Tag', 'woo-checkout-for-digital-goods' ); ?>">
			<?php
			$tags = get_terms(
				array(
					'taxonomy'   => 'product_tag',
					'hide_empty' => true,
					'number'     => 500,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
			if ( ! is_wp_error( $tags ) && is_array( $tags ) ) {
                foreach ( $tags as $tag_term ) {
                    $opt( 'tag_' . (int) $tag_term->term_id, $tag_term->name );
                }
			}
			?>
		</optgroup>
	<?php endif; ?>
</select>
