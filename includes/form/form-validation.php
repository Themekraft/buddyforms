<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The default validation is already covert. Let us do some extra work and also do the advanced validation server site.
 */
add_filter( 'buddyforms_form_custom_validation', 'buddyforms_server_validation', 2, 2 );
/**
 *
 * Server Site Validation
 *
 * @param $valid
 * @param $form_slug
 *
 * @return bool
 */
function buddyforms_server_validation( $valid, $form_slug ) {
	global $buddyforms;

	$form = $buddyforms[ $form_slug ];

	if ( isset( $form['form_fields'] ) ) {
		$global_error = ErrorHandler::get_instance();
		foreach ( $form['form_fields'] as $key => $form_field ) {

			// if field not have a value send in the $_POST pass to next one
			// @since 4.2.3
			if ( ! isset( $_POST[ $form_field['slug'] ] ) ) {
				continue;
			}

			// If the value of the field is empty then don´t run the validation
			// This means that the field is not mandatory and empty values are allowed.
			if ( isset( $_POST[ $form_field['slug'] ] ) ) {
				$field_value = buddyforms_sanitize( '', wp_unslash( $_POST[ $form_field['slug'] ] ) );
				if ( empty( $field_value ) ) {
					continue;
				}
			}

			if ( isset( $form_field['validation_min'] ) && $form_field['validation_min'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || ( ( $form_field['validation_min'] !== $form_field['validation_max'] ) && $_POST[ $form_field['slug'] ] < $form_field['validation_min'] ) ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value greater than or equal to ', 'buddyforms' ) . $form_field['validation_min'];
					$global_error->add_error( new BuddyForms_Error( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] ) );
				}
			}

			if ( isset( $form_field['validation_max'] ) && $form_field['validation_max'] > 0 ) {
				if ( ! is_numeric( $_POST[ $form_field['slug'] ] ) || ( ( $form_field['validation_min'] !== $form_field['validation_max'] ) && $_POST[ $form_field['slug'] ] > $form_field['validation_max'] ) ) {
					$valid                    = false;
					$validation_error_message = __( 'Please enter a value less than or equal to ', 'buddyforms' ) . $form_field['validation_max'];
					$global_error->add_error( new BuddyForms_Error( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] ) );
				}
			}

			if ( isset( $form_field['validation_minlength'] ) && intval( $form_field['validation_minlength'] ) > 0 ) {
				if ( mb_strlen( trim( sanitize_text_field( wp_unslash( $_POST[ $form_field['slug'] ] ) ) ) ) < $form_field['validation_minlength'] ) {
					$valid                    = false;
					$validation_error_message = sprintf( /* translators: %s: $form_field['validation_minlength']. */ __( 'Please enter at least %d characters.', 'buddyforms' ), $form_field['validation_minlength'] );
					$global_error->add_error( new BuddyForms_Error( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] ) );
				}
			}

			if ( isset( $form_field['validation_maxlength'] ) && intval( $form_field['validation_maxlength'] ) > 0 ) {
				if ( mb_strlen( trim( sanitize_text_field( wp_unslash( $_POST[ $form_field['slug'] ] ) ) ) ) > intval( $form_field['validation_maxlength'] ) ) {
					$valid                    = false;
					$validation_error_message = sprintf( /* translators: %s: $form_field['validation_maxlength']. */ __( 'Please enter no more than %d characters.', 'buddyforms' ), $form_field['validation_maxlength'] );
					$global_error->add_error( new BuddyForms_Error( 'buddyforms_form_' . $form_slug, $validation_error_message, $form_field['name'] ) );
				}
			}
		}
	}

	return $valid;
}

function buddyforms_sanitize( $type, $value ) {

	switch ( $type ) {
		case 'subject':
			$value = sanitize_text_field( $value );
			break;
		case 'message':
			$value = esc_textarea( $value );
			break;
		case 'display_name':
			$value = sanitize_text_field( $value );
			break;
		case 'user_login':
			$value = sanitize_user( $value );
			break;
		case 'user_email':
			$value = sanitize_email( $value );
			break;
		case 'user_first':
			$value = sanitize_text_field( $value );
			break;
		case 'user_last':
			$value = sanitize_text_field( $value );
			break;
		case 'user_pass':
			$value = sanitize_text_field( $value );
			break;
		case 'user_website':
			$value = esc_url( $value );
			break;
		case 'user_bio':
			$value = esc_textarea( $value );
			break;
		case 'number':
			$value = is_numeric( $value ) ? $value : 0;
			break;
		case 'title':
			$value = sanitize_text_field( $value );
			break;
		case 'content':
			$value = esc_textarea( $value );
			break;
		case 'mail':
			$value = sanitize_email( $value );
			break;
		case 'textarea':
			$value = wp_kses_post( $value );
			break;
		case 'text':
			$value = sanitize_text_field( $value );
			break;
		case 'link':
			$value = esc_url( $value );
			break;
		default:
			if ( is_array( $value ) ) {
				array_walk_recursive( $value, 'sanitize_text_field' );
			} else {
				$value = apply_filters( 'buddyforms_sanitize', sanitize_text_field( $value ), $type );
			}
			break;
	}

	return $value;
}