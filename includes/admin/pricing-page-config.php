<?php
/**
 * Pricing page filter registration for BuddyForms.
 * Provides bundle credentials, copy, and tier data to the shared pricing-page submodule.
 *
 * @package BuddyForms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'buddyforms_pricing_page_config' ) ) {
	/**
	 * @param array<string,mixed> $config
	 * @return array<string,mixed>
	 */
	function buddyforms_pricing_page_config( $config ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! str_contains( $screen->id, 'buddyforms_bundle_screen' ) ) {
			return $config;
		}

		$config['heading']    = __( 'Get the BuddyForms Bundle', 'buddyforms' );
		$config['subheading'] = __( 'Unlock every BuddyForms add-on with a single license, plus a year of updates and support.', 'buddyforms' );

		$config['bundle'] = array(
			'product_id' => '7487',
			'plan_id'    => '12239',
			'public_key' => 'pk_68d9aeacd7352d37de451d91e3081',
			'name'       => __( 'BuddyForms Bundle', 'buddyforms' ),
		);

		$bullets = array(
			array(
				'label'     => __( 'All BuddyForms add-ons included', 'buddyforms' ),
				'highlight' => true,
			),
			array( 'label' => __( 'BuddyForms', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/frontend-post-form-buddyforms/' ),
			array( 'label' => __( 'BuddyForms ACF', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-advanced-custom-fields/' ),
			array( 'label' => __( 'BuddyForms Anonymous Author', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-anonymous-author/' ),
			array( 'label' => __( 'BuddyForms Attach Post with Group', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddypress-group-post/' ),
			array( 'label' => __( 'BuddyForms Custom Login', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/wordpress-custom-login-page/' ),
			array( 'label' => __( 'BuddyForms Geo My WP', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-geo-my-wp/' ),
			array( 'label' => __( 'BuddyForms Hook Fields', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/display-submissions-data/' ),
			array( 'label' => __( 'BuddyForms Hierarchical Posts', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-hierarchical-posts/' ),
			array( 'label' => __( 'BuddyForms MailPoet', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-mailpoet/' ),
			array( 'label' => __( 'BuddyForms Members', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-buddypress-members/' ),
			array( 'label' => __( 'BuddyForms Moderation', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-content-moderation/' ),
			array( 'label' => __( 'BuddyForms Pay for Submission', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-pay-for-submissions/' ),
			array( 'label' => __( 'BuddyForms Pods', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/wordpress-pods-forms/' ),
			array( 'label' => __( 'BuddyForms Post in Groups', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-buddypress-post-in-groups/' ),
			array( 'label' => __( 'BuddyForms Profile Image', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddypress-profile-image/' ),
			array( 'label' => __( 'BuddyForms Remote', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-remote-embed-forms/' ),
			array( 'label' => __( 'BuddyForms Ultimate Member', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-ultimate-member/' ),
			array( 'label' => __( 'BuddyForms WooCommerce Bookings', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-woocommerce-bookings/' ),
			array( 'label' => __( 'BuddyForms WooCommerce Form Elements', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/woocommerce-form-elements/' ),
			array( 'label' => __( 'BuddyForms WooCommerce Simple Auctions', 'buddyforms' ), 'url' => 'https://themekraft.com/wordpress-products/buddyforms-woocommerce-simple-auction/' ),
			__( 'One year of support', 'buddyforms' ),
			__( 'One year of updates', 'buddyforms' ),
		);

		$config['tiers'] = array(
			array(
				'id'       => 'personal',
				'name'     => __( 'Personal Plan', 'buddyforms' ),
				'sites'    => __( 'One Site', 'buddyforms' ),
				'licenses' => '1',
				'price'    => '99.99',
				'bullets'  => $bullets,
			),
			array(
				'id'        => 'professional',
				'name'      => __( 'Professional Plan', 'buddyforms' ),
				'sites'     => __( 'Five Sites', 'buddyforms' ),
				'licenses'  => '5',
				'price'     => '149.99',
				'highlight' => true,
				'bullets'   => $bullets,
			),
			array(
				'id'       => 'agency',
				'name'     => __( 'Agency Plan', 'buddyforms' ),
				'sites'    => __( 'Unlimited Sites', 'buddyforms' ),
				'licenses' => 'unlimited',
				'price'    => '249.99',
				'bullets'  => $bullets,
			),
		);

		return $config;
	}
}
add_filter( 'tk_pricing_page_config', 'buddyforms_pricing_page_config' );
