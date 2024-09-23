<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );

// Get product details from Freemius via API
$annual_plugin_price = '';
$monthly_plugin_price = '';
$plugin_details = array(
    'product_id' => 44945,
);

$api_url = add_query_arg(wp_rand(), '', WCDG_STORE_URL . 'wp-json/dotstore-product-fs-data/v2/dotstore-product-fs-data');
$final_api_url = add_query_arg($plugin_details, $api_url);

if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
    $api_response = vip_safe_wp_remote_get( $final_api_url, 3, 1, 20 );
} else {
    $api_response = wp_remote_get( $final_api_url ); // phpcs:ignore
}

if ( ( !is_wp_error($api_response)) && (200 === wp_remote_retrieve_response_code( $api_response ) ) ) {
	$api_response_body = wp_remote_retrieve_body($api_response);
	$plugin_pricing = json_decode( $api_response_body, true );

	if ( isset( $plugin_pricing ) && ! empty( $plugin_pricing ) ) {
		$first_element = reset( $plugin_pricing );
        if ( ! empty( $first_element['price_data'] ) ) {
            $first_price = reset( $first_element['price_data'] )['annual_price'];
        } else {
            $first_price = "0";
        }

        if( "0" !== $first_price ){
        	$annual_plugin_price = $first_price;
        	$monthly_plugin_price = round( intval( $first_price  ) / 12 );
        }
	}
}

// Set plugin key features content
$plugin_key_features = array(
    array(
        'title' => esc_html__( 'Exclude Fields During Checkout', 'woo-checkout-for-digital-goods' ),
        'description' => esc_html__( 'Fewer fields lead to a faster checkout, encouraging more users to complete their purchases.', 'woo-checkout-for-digital-goods' ),
        'popup_image' => esc_url( WCDG_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-one-img.png' ),
        'popup_content' => array(
        	esc_html__( 'Excluding unnecessary fields during checkout can significantly streamline the user experience, reducing friction and improving conversion rates.', 'woo-checkout-for-digital-goods' )
        ),
        'popup_examples' => array(
            esc_html__( 'Commonly Excluded Fields are Company Name, Address Line 2, Order note, etc.', 'woo-checkout-for-digital-goods' ),
            esc_html__( 'We only ask for the most critical information: shipping address, payment method, and contact details. Fields like "Company Name" or "Fax Number," which might be useful for B2B transactions, are excluded because they are irrelevant to most consumers.', 'woo-checkout-for-digital-goods' ),
        )
    ),
    array(
        'title' => esc_html__( 'Role-Based Checkout Optimization', 'woo-checkout-for-digital-goods' ),
        'description' => esc_html__( 'Enabling faster transactions for specific user roles optimizes the checkout process, improving user experience and boosting conversions.', 'woo-checkout-for-digital-goods' ),
        'popup_image' => esc_url( WCDG_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-two-img.png' ),
        'popup_content' => array(
        	esc_html__( 'By enabling quick checkout for wholesale user roles, the store significantly reduces the time it takes for these buyers to complete their purchases.', 'woo-checkout-for-digital-goods' ),
        ),
        'popup_examples' => array(
            esc_html__( 'This feature is particularly useful for stores that cater to different types of customers, such as wholesale buyers and regular consumers.', 'woo-checkout-for-digital-goods' ),
            esc_html__( 'This "Quick Checkout" option, allows them to bypass the standard multi-step checkout process.', 'woo-checkout-for-digital-goods' ),
        )
    ),
    array(
        'title' => esc_html__( 'Quick Checkout for Products, Categories & Tags', 'woo-checkout-for-digital-goods' ),
        'description' => esc_html__( 'Simplify the purchase process by enabling quick checkout for selected products, categories, or tags in your WooCommerce store, enhancing customer convenience and satisfaction.', 'woo-checkout-for-digital-goods' ),
        'popup_image' => esc_url( WCDG_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-three-img.png' ),
        'popup_content' => array(
        	esc_html__( 'By enabling quick checkout for specific products, categories, and tags, the Etsy seller capitalizes on the urgency of seasonal shopping.', 'woo-checkout-for-digital-goods' ),
        ),
        'popup_examples' => array(
            esc_html__( 'E.g., The seller enables the "Quick Checkout" option for all products within a specific category, as well as for products tagged with “gift” or “sale” during festival season.', 'woo-checkout-for-digital-goods' ),
            esc_html__( 'This feature is particularly useful during promotions or for best-selling items that customers are likely to purchase quickly without needing to browse further.', 'woo-checkout-for-digital-goods' )
        )
    ),
    array(
        'title' => esc_html__( 'Quick Checkout from Shop or Product Details Page', 'woo-checkout-for-digital-goods' ),
        'description' => esc_html__( 'Enable customers to complete their purchase directly from the shop or product details page, streamlining the buying process for a faster checkout experience.', 'woo-checkout-for-digital-goods' ),
        'popup_image' => esc_url( WCDG_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-four-img.png' ),
        'popup_content' => array(
        	esc_html__( 'By offering quick checkout options directly from both the shop and product details pages, We can reduce the time and effort required for customers to complete their purchases.', 'woo-checkout-for-digital-goods' ),
        ),
        'popup_examples' => array(
            esc_html__( 'If the customer clicks "Quick Checkout," they are immediately taken to a quick checkout process where they can finalize their purchase without going through multiple steps or additional pages.', 'woo-checkout-for-digital-goods' ),
            esc_html__( 'This convenience is particularly beneficial for returning customers who already know what they want and prefer a faster shopping experience.', 'woo-checkout-for-digital-goods' ),
        )
    ),
    array(
        'title' => esc_html__( 'Exclude Order Note', 'woo-checkout-for-digital-goods' ),
        'description' => esc_html__( 'Eliminate the order note from the checkout process wherever it looks unnecessary, and let your buyers make their own purchasing decisions.', 'woo-checkout-for-digital-goods' ),
        'popup_image' => esc_url( WCDG_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-five-img.png' ),
        'popup_content' => array(
        	esc_html__( 'The streamlined experience ensures that customers can complete their purchases quickly and without unnecessary distractions, contributing to higher conversion rates and customer satisfaction.', 'woo-checkout-for-digital-goods' ),
        ),
        'popup_examples' => array(
            esc_html__( 'When customers shop in the store and proceed to checkout, they are not prompted to leave an "Order Note" or special instructions.', 'woo-checkout-for-digital-goods' ),
            esc_html__( 'By excluding the "Order Note" field, we speed up the checkout process, minimizing potential friction points that could lead to cart abandonment.', 'woo-checkout-for-digital-goods' ),
        )
    ),
    array(
        'title' => esc_html__( 'Import/Export Configuration', 'woo-checkout-for-digital-goods' ),
        'description' => esc_html__( 'A Digital Goods plugin\'s Import/Export configuration feature is essential for efficiently managing and transferring settings, products, and other data between different environments or backups.', 'woo-checkout-for-digital-goods' ),
        'popup_image' => esc_url( WCDG_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-six-img.png' ),
        'popup_content' => array(
        	esc_html__( 'It allows store owners to import and export configuration settings, product data, and other essential information regarding our plugin.', 'woo-checkout-for-digital-goods' ),
        ),
        'popup_examples' => array(
            esc_html__( 'The ability to import and export configurations in-store simplifies the process of launching a new store version, migrating data, or creating backups.', 'woo-checkout-for-digital-goods' ),
            esc_html__( 'It reduces the risk of errors during migration, saves time by automating data transfer, and ensures consistency across different environments.', 'woo-checkout-for-digital-goods' ),
        )
    ),
);
?>
	<div class="dotstore-upgrade-dashboard">
		<div class="premium-benefits-section">
			<h2><?php esc_html_e( 'Upgrade to Unlock Premium Features', 'woo-checkout-for-digital-goods' ); ?></h2>
			<p><?php esc_html_e( 'Check out the advanced features, simplify checkout management & increase returns by upgrading to premium!', 'woo-checkout-for-digital-goods' ); ?></p>
		</div>
		<div class="premium-plugin-details">
			<div class="premium-key-fetures">
				<h3><?php esc_html_e( 'Discover Our Top Key Features', 'woo-checkout-for-digital-goods' ); ?></h3>
				<ul>
					<?php 
					if ( isset( $plugin_key_features ) && ! empty( $plugin_key_features ) ) {
						foreach( $plugin_key_features as $key_feature ) {
							?>
							<li>
								<h4><?php echo esc_html( $key_feature['title'] ); ?><span class="premium-feature-popup"></span></h4>
								<p><?php echo esc_html( $key_feature['description'] ); ?></p>
								<div class="feature-explanation-popup-main">
									<div class="feature-explanation-popup-outer">
										<div class="feature-explanation-popup-inner">
											<div class="feature-explanation-popup">
												<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-checkout-for-digital-goods'); ?>"></span>
												<div class="popup-body-content">
													<div class="feature-content">
														<h4><?php echo esc_html( $key_feature['title'] ); ?></h4>
														<?php 
														if ( isset( $key_feature['popup_content'] ) && ! empty( $key_feature['popup_content'] ) ) {
															foreach( $key_feature['popup_content'] as $feature_content ) {
																?>
																<p><?php echo esc_html( $feature_content ); ?></p>
																<?php
															}
														}
														?>
														<ul>
															<?php 
															if ( isset( $key_feature['popup_examples'] ) && ! empty( $key_feature['popup_examples'] ) ) {
																foreach( $key_feature['popup_examples'] as $feature_example ) {
																	?>
																	<li><?php echo esc_html( $feature_example ); ?></li>
																	<?php
																}
															}
															?>
														</ul>
													</div>
													<div class="feature-image">
														<img src="<?php echo esc_url( $key_feature['popup_image'] ); ?>" alt="<?php echo esc_attr( $key_feature['title'] ); ?>">
													</div>
												</div>
											</div>		
										</div>
									</div>
								</div>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
			<div class="premium-plugin-buy">
				<div class="premium-buy-price-box">
					<div class="price-box-top">
						<div class="pricing-icon">
							<img src="<?php echo esc_url( WCDG_PLUGIN_URL . 'admin/images/premium-upgrade-img/pricing-1.svg' ); ?>" alt="<?php esc_attr_e( 'Personal Plan', 'woo-checkout-for-digital-goods' ); ?>">
						</div>
						<h4><?php esc_html_e( 'Personal', 'woo-checkout-for-digital-goods' ); ?></h4>
					</div>
					<div class="price-box-middle">
						<?php
						if ( ! empty( $annual_plugin_price ) ) {
							?>
							<div class="monthly-price-wrap"><?php echo esc_html( '$' . $monthly_plugin_price ); ?><span class="seprater">/</span><span><?php esc_html_e( 'month', 'woo-checkout-for-digital-goods' ); ?></span></div>
							<div class="yearly-price-wrap"><?php echo sprintf( esc_html__( 'Pay $%s today. Renews in 12 months.', 'woo-checkout-for-digital-goods' ), esc_html( $annual_plugin_price ) ); ?></div>
							<?php	
						}
						?>
						<span class="for-site"><?php esc_html_e( '1 site', 'woo-checkout-for-digital-goods' ); ?></span>
						<p class="price-desc"><?php esc_html_e( 'Great for website owners with a single WooCommerce Site', 'woo-checkout-for-digital-goods' ); ?></p>
					</div>
					<div class="price-box-bottom">
						<a href="javascript:void(0);" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'woo-checkout-for-digital-goods' ); ?></a>
						<p class="trusted-by"><?php esc_html_e( 'Trusted by 100,000+ store owners and WP experts!', 'woo-checkout-for-digital-goods' ); ?></p>
					</div>
				</div>
				<div class="premium-satisfaction-guarantee premium-satisfaction-guarantee-2">
					<div class="money-back-img">
						<img src="<?php echo esc_url(WCDG_PLUGIN_URL . 'admin/images/premium-upgrade-img/14-Days-Money-Back-Guarantee.png'); ?>" alt="<?php esc_attr_e('14-Day money-back guarantee', 'woo-checkout-for-digital-goods'); ?>">
					</div>
					<div class="money-back-content">
						<h2><?php esc_html_e( '14-Day Satisfaction Guarantee', 'woo-checkout-for-digital-goods' ); ?></h2>
						<p><?php esc_html_e( 'You are fully protected by our 100% Satisfaction Guarantee. If over the next 14 days you are unhappy with our plugin or have an issue that we are unable to resolve, we\'ll happily consider offering a 100% refund of your money.', 'woo-checkout-for-digital-goods' ); ?></p>
					</div>
				</div>
				<div class="plugin-customer-review">
					<h3><?php esc_html_e( 'Works Perfectly and Simple to Use!', 'woo-checkout-for-digital-goods' ); ?></h3>
					<p>
						<?php echo wp_kses( __( 'Tried other Woocommerce checkout form field editors, too complicated for what I needed. <strong>This one is ideal to eliminate customer info that’s not needed</strong> for digital goods, and reduce friction for customers.', 'woo-checkout-for-digital-goods' ), array(
				                'strong' => array(),
				            ) ); 
			            ?>
		            </p>
					<div class="review-customer">
						<div class="customer-img">
							<img src="<?php echo esc_url(WCDG_PLUGIN_URL . 'admin/images/premium-upgrade-img/customer-profile-img.jpeg'); ?>" alt="<?php esc_attr_e('Customer Profile Image', 'woo-checkout-for-digital-goods'); ?>">
						</div>
						<div class="customer-name">
							<span><?php esc_html_e( 'Jevor B.', 'woo-checkout-for-digital-goods' ); ?></span>
							<div class="customer-rating-bottom">
								<div class="customer-ratings">
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
									<span class="dashicons dashicons-star-filled"></span>
								</div>
								<div class="verified-customer">
									<span class="dashicons dashicons-yes-alt"></span>
									<?php esc_html_e( 'Verified Customer', 'woo-checkout-for-digital-goods' ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="upgrade-to-pro-faqs">
			<h2><?php esc_html_e( 'FAQs', 'woo-checkout-for-digital-goods' ); ?></h2>
			<div class="upgrade-faqs-main">
				<div class="upgrade-faqs-list">
					<div class="upgrade-faqs-header">
						<h3><?php esc_html_e( 'Do you offer support for the plugin? What’s it like?', 'woo-checkout-for-digital-goods' ); ?></h3>
					</div>
					<div class="upgrade-faqs-body">
						<p>
						<?php 
							echo sprintf(
							    esc_html__('Yes! You can read our %s or submit a %s. We are very responsive and strive to do our best to help you.', 'woo-checkout-for-digital-goods'),
							    '<a href="' . esc_url('https://docs.thedotstore.com/collection/165-digital-goods-for-checkout') . '" target="_blank">' . esc_html__('knowledge base', 'woo-checkout-for-digital-goods') . '</a>',
							    '<a href="' . esc_url('https://www.thedotstore.com/support-ticket/') . '" target="_blank">' . esc_html__('support ticket', 'woo-checkout-for-digital-goods') . '</a>',
							);

						?>
						</p>
					</div>
				</div>
				<div class="upgrade-faqs-list">
					<div class="upgrade-faqs-header">
						<h3><?php esc_html_e( 'What payment methods do you accept?', 'woo-checkout-for-digital-goods' ); ?></h3>
					</div>
					<div class="upgrade-faqs-body">
						<p><?php esc_html_e( 'You can pay with your credit card using Stripe checkout. Or your PayPal account.', 'woo-checkout-for-digital-goods' ); ?></p>
					</div>
				</div>
				<div class="upgrade-faqs-list">
					<div class="upgrade-faqs-header">
						<h3><?php esc_html_e( 'What’s your refund policy?', 'woo-checkout-for-digital-goods' ); ?></h3>
					</div>
					<div class="upgrade-faqs-body">
						<p><?php esc_html_e( 'We have a 14-day money-back guarantee.', 'woo-checkout-for-digital-goods' ); ?></p>
					</div>
				</div>
				<div class="upgrade-faqs-list">
					<div class="upgrade-faqs-header">
						<h3><?php esc_html_e( 'I have more questions…', 'woo-checkout-for-digital-goods' ); ?></h3>
					</div>
					<div class="upgrade-faqs-body">
						<p>
						<?php 
							echo sprintf(
							    esc_html__('No problem, we’re happy to help! Please reach out at %s.', 'woo-checkout-for-digital-goods'),
							    '<a href="' . esc_url('mailto:hello@thedotstore.com') . '" target="_blank">' . esc_html('hello@thedotstore.com') . '</a>',
							);

						?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="upgrade-to-premium-btn">
			<a href="javascript:void(0);" target="_blank" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'woo-checkout-for-digital-goods' ); ?><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="crown" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-crown fa-w-20 fa-3x" width="22" height="20"><path fill="#000" d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z" class=""></path></svg></a>
		</div>
	</div>
</div>
</div>
</div>
</div>
<?php 
