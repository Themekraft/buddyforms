<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use tk\GuzzleHttp\Client;
use tk\GuzzleHttp\Psr7\Request;

add_action( 'wp_ajax_buddyforms_post_types_taxonomies', 'buddyforms_post_types_taxonomies' );
function buddyforms_post_types_taxonomies() {

	if ( ! isset( $_POST['post_type'] ) ) {
		echo 'false';
		die();
	}

	$post_type             = buddyforms_sanitize( '', wp_unslash( $_POST['post_type'] ) );
	$buddyforms_taxonomies = buddyforms_taxonomies( $post_type );

	$tmp = '';
	foreach ( $buddyforms_taxonomies as $name => $label ) {
		$tmp .= '<option value="' . $name . '">' . $label . '</option>';
	}

	echo wp_kses( $tmp, buddyforms_wp_kses_allowed_atts() );
	die();

}

add_action( 'wp_ajax_buddyforms_close_submission_default_page_notification', 'buddyforms_close_submission_default_page_notification' );
/**
 * @return bool
 */
function buddyforms_close_submission_default_page_notification() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		die();
	}
	if ( ! isset( $_POST['action'] ) || wp_verify_nonce( $_POST['nonce'], 'fac_drop' ) === false || $_POST['action'] !== 'buddyforms_close_submission_default_page_notification' ) {
		die();
	}
	update_option( 'close_submission_default_page_notification', 1 );
	die();
}

add_action( 'wp_ajax_buddyforms_update_taxonomy_default', 'buddyforms_update_taxonomy_default' );
function buddyforms_update_taxonomy_default() {

	if ( ! isset( $_POST['taxonomy'] ) || $_POST['taxonomy'] == 'none' ) {
		$tmp = '<option value="none">' . __( 'First you need to select a Taxonomy to select the Taxonomy defaults', 'buddyforms' ) . '</option>';
		echo wp_kses( $tmp, buddyforms_wp_kses_allowed_atts() );
		die();
	}

	$taxonomy = buddyforms_sanitize( '', wp_unslash( $_POST['taxonomy'] ) );

	$args = array(
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => false,
		'fields'     => 'id=>name',
	);

	$terms = get_terms( $taxonomy, $args );

	$tmp = '<option value="none">none</option>';
	foreach ( $terms as $key => $term_name ) {
		$tmp .= '<option value="' . $key . '">' . $term_name . '</option>';
	}

	echo wp_kses( $tmp, buddyforms_wp_kses_allowed_atts() );

	die();

}

add_action( 'wp_ajax_buddyforms_new_page', 'buddyforms_new_page' );
/**
 * Create the holder page to be use as endpoint
 */
function buddyforms_new_page() {

	check_ajax_referer( 'fac_drop', 'nonce' );
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Check if a title is entered
	if ( empty( $_POST['page_name'] ) ) {
		$json['error'] = __( 'Please enter a name', 'buddyforms' );
		echo json_encode( $json );
		die();
	}

	// Create post object
	$new_page = array(
		'post_title'   => wp_strip_all_tags( wp_unslash( $_POST['page_name'] ) ),
		'post_content' => '',
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);

	// Insert the post into the database
	$new_page = wp_insert_post( $new_page );

	// Check if page creation worked successfully
	if ( is_wp_error( $new_page ) ) {
		$json['error'] = $new_page;
	} else {
		$json['id']   = $new_page;
		$json['name'] = wp_strip_all_tags( wp_unslash( $_POST['page_name'] ) );
	}

	echo json_encode( $json );
	die();

}

add_action( 'wp_ajax_buddyforms_url_builder', 'buddyforms_url_builder' );
function buddyforms_url_builder() {
	global $post;
	$page_id   = filter_var( wp_unslash( $_POST['attached_page'] ), FILTER_VALIDATE_INT );
	$form_slug = filter_var( wp_unslash( $_POST['form_slug'] ), FILTER_SANITIZE_STRING );
	$post      = get_post( $page_id );

	if ( isset( $post->post_name ) ) {
		$json['permalink'] = get_permalink( $page_id );
		$json['form_slug'] = $form_slug;
		echo json_encode( $json );
		die();
	}
	echo json_encode( 'none' );
	die();
}

function buddyforms_custom_form_template_tracking() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		die();
	}
	if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
		die();
	}
	if ( ! wp_verify_nonce( $_POST['nonce'], 'fac_drop' ) ) {
		die();
	}
	buddyforms_track(
		'selected-form-template',
		array(
			'template' => 'custom',
			'type'     => 'custom',
		)
	);
}

add_action( 'wp_ajax_buddyforms_custom_form_template', 'buddyforms_custom_form_template_tracking' );
