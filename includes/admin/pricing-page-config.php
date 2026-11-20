<?php
/**
 * Pricing page filter registration for BuddyForms.
 * The Go Pro page sells BuddyForms Pro. The BuddyForms add-ons are free, so the page does not list them.
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

		$config['heading']    = __( 'Go Pro to publish anything from the front end', 'buddyforms' );
		$config['subheading'] = __( 'The free version builds contact, registration and post forms. Pro turns BuddyForms into a full front-end publishing system.', 'buddyforms' );

		$config['bundle'] = array(
			'product_id' => '391',
			'plan_id'    => '583',
			'public_key' => 'pk_dea3d8c1c831caf06cfea10c7114c',
			'name'       => __( 'BuddyForms Pro', 'buddyforms' ),
		);

		$bullets = array(
			array(
				'label'     => __( 'All BuddyForms Pro features unlocked', 'buddyforms' ),
				'highlight' => true,
			),
			__( 'Any post type: WooCommerce products, events, listings or any custom post type', 'buddyforms' ),
			__( 'Permissions by role: choose which roles can use a form, and create, edit and delete posts', 'buddyforms' ),
			__( 'Post status, revisions and notifications: drafts, default status, front-end revisions and status emails', 'buddyforms' ),
			__( 'Registration options: auto-generated passwords, username from the email address, moderated profile updates', 'buddyforms' ),
			__( 'File uploads with size and file-type limits', 'buddyforms' ),
			__( 'More fields: Taxonomy, Range slider, Price, State, Hidden, HTML and Form Actions', 'buddyforms' ),
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
