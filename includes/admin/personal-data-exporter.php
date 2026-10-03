<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function buddyforms_exporter( $email_address, $page = 1 ) {
	global $buddyforms;

	$number       = 500; // Limit us to avoid timing out
	$page         = (int) $page;
	$export_items = array();
	$done         = true;

	// Only entries written by the user who owns the requested email address belong in the export.
	$user = get_user_by( 'email', $email_address );
	if ( empty( $user ) || empty( $buddyforms ) || ! is_array( $buddyforms ) ) {
		return array(
			'data' => $export_items,
			'done' => $done,
		);
	}

	foreach ( $buddyforms as $form_slug => $buddyform ) {
		if ( empty( $buddyform['post_type'] ) ) {
			continue;
		}

		$query_args = array(
			'post_type'      => $buddyform['post_type'],
			'author'         => $user->ID,
			'post_status'    => 'any',
			'meta_key'       => '_bf_form_slug',
			'meta_value'     => $form_slug,
			'posts_per_page' => $number,
			'paged'          => $page,
			'no_found_rows'  => true,
		);

		$the_query = new WP_Query( $query_args );

		foreach ( $the_query->posts as $post ) {
			$my_data   = array();
			$my_data[] = array(
				'name'  => __( 'Title', 'buddyforms' ),
				'value' => get_the_title( $post ),
			);
			$my_data[] = array(
				'name'  => __( 'Content', 'buddyforms' ),
				'value' => $post->post_content,
			);

			if ( isset( $buddyform['form_fields'] ) ) {
				foreach ( $buddyform['form_fields'] as $field_key => $field ) {
					$value     = get_post_meta( $post->ID, $field['slug'], true );
					$my_data[] = array(
						'name'  => $field['name'],
						'value' => is_array( $value ) ? implode( ', ', array_map( 'strval', array_filter( $value, 'is_scalar' ) ) ) : $value,
					);
				}
			}

			$export_items[] = array(
				'group_id'    => $buddyform['slug'],
				'group_label' => $buddyform['name'],
				'item_id'     => "buddyform-{$buddyform['slug']}-{$post->ID}",
				'data'        => $my_data,
			);
		}

		if ( count( $the_query->posts ) === $number ) {
			$done = false;
		}
	}

	return array(
		'data' => $export_items,
		'done' => $done,
	);
}

function buddyforms_register_exporter( $exporters ) {
	$exporters['buddyforms'] = array(
		'exporter_friendly_name' => __( 'BuddyForms', 'buddyforms' ),
		'callback'               => 'buddyforms_exporter',
	);

	return $exporters;
}

add_filter(
	'wp_privacy_personal_data_exporters',
	'buddyforms_register_exporter',
	10
);
