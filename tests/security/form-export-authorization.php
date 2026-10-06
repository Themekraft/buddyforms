<?php
/**
 * Run on a disposable site:
 * wp --skip-plugins --skip-themes eval-file tests/security/form-export-authorization.php
 * Loads the real handler with WordPress users, capabilities, metadata and nonces.
 * Separate processes allow the download handler to exit normally.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

require_once dirname( __DIR__, 2 ) . '/includes/admin/register-post-types.php';
buddyforms_register_post_type();
// Isolate fixture writes from the builder's save and cache regeneration hooks.
remove_action( 'save_post', 'buddyforms_edit_form_save_meta_box_data' );
remove_action( 'transition_post_status', 'buddyforms_transition_post_status_regenerate_global_options', 10 );

if ( ! empty( $args ) ) {
	$case = json_decode( base64_decode( $args[0] ), true );
	wp_set_current_user( $case['user'] );
	$GLOBALS['post'] = get_post( $case['form'] );
	$_SERVER['REQUEST_URI'] = '/wp-admin/profile.php';
	$_REQUEST = $case['request'];
	if ( isset( $case['nonce_form'] ) ) {
		$nonce_user = get_current_user_id();
		if ( isset( $case['nonce_user'] ) ) {
			wp_set_current_user( $case['nonce_user'] );
		}
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'buddyforms_export_form_' . $case['nonce_form'] );
		wp_set_current_user( $nonce_user );
	}
	class BuddyFormsExportTestDenied extends Exception {}
	add_filter( 'wp_die_handler', function () {
		return function ( $message, $title, $options ) {
			throw new BuddyFormsExportTestDenied( '', isset( $options['response'] ) ? $options['response'] : 500 );
		};
	}, PHP_INT_MAX );
	if ( ! empty( $case['row'] ) ) {
		$actions = apply_filters( 'post_row_actions', array(), $GLOBALS['post'] );
		$valid = false;
		if ( isset( $actions['export'] ) ) {
			preg_match( '/href="([^"]+)"/', $actions['export'], $matches );
			parse_str( wp_parse_url( html_entity_decode( $matches[1] ), PHP_URL_QUERY ), $query );
			$valid = isset( $query['_wpnonce'] ) && wp_verify_nonce( $query['_wpnonce'], 'buddyforms_export_form_' . $case['form'] );
		}
		echo wp_json_encode( array( 'export_link' => isset( $actions['export'] ), 'valid_nonce' => (bool) $valid ) );
	} else {
		try {
			do_action( 'admin_init' );
			echo wp_json_encode( array( 'no_export' => true ) );
		} catch ( BuddyFormsExportTestDenied $error ) {
			echo wp_json_encode( array( 'denied' => $error->getCode() ) );
		}
	}
	return;
}

$users = $posts = array();
$passed = $failed = 0;
$options = array( 'name' => 'Export authorization fixture', 'form_fields' => array( 'test' => array( 'type' => 'text' ) ) );
try {
	foreach ( array( 'subscriber', 'administrator', 'author' ) as $role ) {
		$id = wp_insert_user( array( 'user_login' => 'bf-export-' . wp_generate_uuid4(), 'user_pass' => wp_generate_password(), 'role' => $role ) );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Unable to create user fixture' );
		}
		$users[ $role ] = $id;
	}
	foreach ( array( 'form' => 'buddyforms', 'other_form' => 'buddyforms', 'ordinary_post' => 'post' ) as $name => $type ) {
		$id = wp_insert_post( array( 'post_type' => $type, 'post_status' => 'publish', 'post_title' => 'BF export ' . wp_generate_uuid4(), 'post_author' => 'form' === $name ? $users['author'] : $users['administrator'] ), true );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Unable to create post fixture' );
		}
		$posts[ $name ] = $id;
		update_post_meta( $id, '_buddyforms_options', $options );
	}
	$form = $posts['form'];
	$base = array( 'user' => $users['administrator'], 'form' => $form, 'request' => array( 'my_action' => 'export_form', 'post_id' => (string) $form ), 'nonce_form' => $form );
	$cases = array();
	$cases['subscriber request from profile.php is denied'] = array_replace( $base, array( 'user' => $users['subscriber'], 'nonce_form' => null ) );
	$cases['subscriber with a valid nonce is denied'] = array_replace( $base, array( 'user' => $users['subscriber'] ) );
	$cases['guest with a valid nonce is denied'] = array_replace( $base, array( 'user' => 0 ) );
	$cases['administrator without a nonce is denied'] = array_replace( $base, array( 'nonce_form' => null ) );
	$cases['nonce for another form is denied'] = array_replace( $base, array( 'nonce_form' => $posts['other_form'] ) );
	$cases['nonce for another user is denied'] = array_replace( $base, array( 'nonce_user' => $users['subscriber'] ) );
	foreach ( array( 'invalid' => 'invalid', 'array' => array( 'invalid' ) ) as $name => $nonce ) {
		$case = $base;
		unset( $case['nonce_form'] );
		$case['request']['_wpnonce'] = $nonce;
		$cases[ $name . ' nonce is denied' ] = $case;
	}
	foreach ( array( 'missing' => null, 'array' => array( $form ), 'negative' => -$form, 'zero' => 0, 'fractional' => $form . '.5', 'nonnumeric' => 'invalid', 'nonexistent' => PHP_INT_MAX, 'wrong post type' => $posts['ordinary_post'] ) as $name => $id ) {
		$case = $base;
		$case['request']['post_id'] = $id;
		$cases[ $name . ' post ID is denied' ] = $case;
	}
	$cases['author cannot export another user\'s form'] = array_replace( $base, array( 'user' => $users['author'], 'request' => array( 'my_action' => 'export_form', 'post_id' => $posts['other_form'] ), 'nonce_form' => $posts['other_form'] ) );
	$cases['administrator can export the form'] = $base;
	$cases['author can export their own form'] = array_replace( $base, array( 'user' => $users['author'] ) );
	$cases['administrator export link has a valid nonce'] = array_replace( $base, array( 'row' => true ) );
	$cases['subscriber has no export link'] = array_replace( $base, array( 'user' => $users['subscriber'], 'row' => true ) );
	$cases['unrelated admin request is ignored'] = array_replace( $base, array( 'request' => array() ) );
	$cases['array action is ignored'] = array_replace( $base, array( 'request' => array( 'my_action' => array( 'export_form' ) ) ) );
	foreach ( $cases as $name => $case ) {
		$result = WP_CLI::launch_self( 'eval-file', array( __FILE__, base64_encode( wp_json_encode( $case ) ) ), array( 'skip-plugins' => true, 'skip-themes' => true ), false, true );
		$response = json_decode( trim( $result->stdout ), true );
		$expected = array( 'denied' => 403 );
		if ( false !== strpos( $name, 'can export' ) ) {
			$expected = $options;
		} elseif ( ! empty( $case['row'] ) ) {
			$allowed = $case['user'] === $users['administrator'];
			$expected = array( 'export_link' => $allowed, 'valid_nonce' => $allowed );
		} elseif ( false !== strpos( $name, 'ignored' ) ) {
			$expected = array( 'no_export' => true );
		}
		$ok = 0 === $result->return_code && $expected === $response;
		$ok ? ++$passed : ++$failed;
		WP_CLI::line( ( $ok ? 'PASS ' : 'FAIL ' ) . $name );
	}
} finally {
	foreach ( $posts as $id ) {
		wp_delete_post( $id, true );
	}
	require_once ABSPATH . 'wp-admin/includes/user.php';
	foreach ( $users as $id ) {
		wp_delete_user( $id );
	}
}
WP_CLI::line( sprintf( 'Results: %d passed, %d failed', $passed, $failed ) );
if ( $failed ) {
	throw new Exception( 'Form export authorization regression failed' );
}
