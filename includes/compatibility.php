<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add compatibility with Better Notifications for WordPress
 *
 * @url https://wordpress.org/plugins/bnfw
 * @url https://betternotificationsforwp.com/documentation/compatibility/support-plugins-front-end-forms/
 */

add_filter( 'bnfw_trigger_insert_post', '__return_true' );
add_action( 'wp_enqueue_scripts', 'front_js_loader1', 1, 1 );

function front_js_loader1() {
	$current_theme = wp_get_theme()->get_template();
	if ( $current_theme === 'enfold' || $current_theme === 'enfold-child' ) {
		$url_force = get_site_url() . '/wp-includes/css/media-views.css';
		wp_register_style( 'media-views-alternative', $url_force );
		wp_enqueue_style( 'media-views-alternative' );
	}
}

/**
 * Let the running copy of BuddyForms satisfy "Requires Plugins: buddyforms".
 *
 * WordPress (6.5+) matches plugin dependencies by folder name, and the premium
 * build lives in `buddyforms-premium`. Without this, add-ons that declare
 * BuddyForms as a dependency could not be activated next to the premium build.
 * ACF PRO registers the same mapping for `advanced-custom-fields` since 6.8.2;
 * older ACF PRO versions are covered here too.
 *
 * @param string $slug Dependency slug from a plugin's "Requires Plugins" header.
 *
 * @return string
 */
function buddyforms_plugin_dependencies_slug( $slug ) {
	if ( 'buddyforms' === $slug ) {
		return basename( BUDDYFORMS_INSTALL_PATH );
	}

	if ( 'advanced-custom-fields' === $slug && defined( 'ACF_PRO' ) && ACF_PRO && defined( 'ACF_PATH' ) ) {
		return basename( ACF_PATH );
	}

	return $slug;
}

add_filter( 'wp_plugin_dependencies_slug', 'buddyforms_plugin_dependencies_slug' );
