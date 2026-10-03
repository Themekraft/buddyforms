<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//
// Post metabox to display form elements in the admin backend
//
function buddyforms_admin_form_metabox() {
	global $buddyforms, $post;

	// sanity check
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	if ( $post->post_type == 'buddyforms' ) {
		return;
	}

	$form_slug                    = get_post_meta( $post->ID, '_bf_form_slug', true );
	$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );

	if ( ! $form_slug ) {
		$form_slug = isset( $buddyforms_posttypes_default[ $post->post_type ] ) ? $buddyforms_posttypes_default[ $post->post_type ] : null;
	}

	if ( empty( $form_slug ) ) {
		return;
	}
	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	$form = $buddyforms[ $form_slug ];

	$metabox_enabled = false;
	if ( isset( $form['form_fields'] ) ) {
		foreach ( $form['form_fields'] as $field_key => $field ) {
			if ( isset( $field['metabox_enabled'] ) && $field['metabox_enabled'] ) {
				$metabox_enabled = true;
			}
		}
	}

	if ( $metabox_enabled ) {
		add_meta_box(
			'buddyforms_form_' . $form_slug,
			'BuddyForms Form: ' . $form['name'],
			'buddyforms_metabox_admin_form_metabox',
			$form['post_type'],
			'normal',
			'high',
			array(
				'form_slug' => $form_slug,
				'form'      => $form,
			)
		);
	}

}

add_action( 'add_meta_boxes', 'buddyforms_admin_form_metabox' );

/**
 * BuddyForm metabox content
 *
 * @param $post
 * @param $metabox
 *
 * @since 2.5.14 Added the parameter to the function
 */
function buddyforms_metabox_admin_form_metabox( $post, $metabox ) {
	global $buddyforms, $post;

	$form_slug = get_post_meta( $post->ID, '_bf_form_slug', true );

	$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );

	if ( ! $form_slug ) {
		$form_slug = $buddyforms_posttypes_default[ $post->post_type ];
	}

	if ( ! isset( $form_slug ) ) {
		return;
	}
	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	$fields         = $buddyforms[ $form_slug ]['form_fields'];
	$metabox_fields = array();
	foreach ( $fields as $field_key => $field ) {
		if ( isset( $field['metabox_enabled'] ) ) {
			$metabox_fields[ $field_key ] = $field;
		}
	}

	echo wp_kses( buddyforms_create_form_metabox( $form_slug, $metabox_fields, $post->ID, $buddyforms[ $form_slug ]['post_type'] ), buddyforms_wp_kses_allowed_atts() );
}

//
// Save the metabox data
//
/**
 * @param $post_id
 */
function buddyforms_metabox_admin_form_metabox_save( $post_id ) {
	global $buddyforms;

	if ( ! is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );

	if ( ! isset( $form_slug ) ) {
		return;
	}
	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return;
	}

	buddyforms_update_post_meta( $post_id, $buddyforms[ $form_slug ]['form_fields'] );
}

add_action( 'save_post', 'buddyforms_metabox_admin_form_metabox_save' );
