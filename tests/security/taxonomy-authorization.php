<?php
/**
 * Run on a disposable site: wp eval-file tests/security/taxonomy-authorization.php
 * Uses the registered AJAX handlers and real WordPress queries and users.
 * Fixture terms and users are deleted even when an assertion fails.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
class BuddyFormsTaxonomyTestExit extends Exception {}
add_filter( 'wp_die_ajax_handler', function () {
	return function () { throw new BuddyFormsTaxonomyTestExit(); };
} );
function buddyforms_test_taxonomy_request( $request ) {
	$_POST = array_merge( array(
		'action' => 'bf_load_taxonomy', 'nonce' => wp_create_nonce( 'bf_tax_loading' ),
		'form_slug' => 'bf-security-public', 'field_slug' => 'terms', 'taxonomy' => 'bf_test_public',
	), $request );
	ob_start();
	try { do_action( get_current_user_id() ? 'wp_ajax_bf_load_taxonomy' : 'wp_ajax_nopriv_bf_load_taxonomy' ); }
	catch ( BuddyFormsTaxonomyTestExit $e ) {}
	return json_decode( ob_get_clean(), true );
}
$original_forms = $GLOBALS['buddyforms'];
$original_user = get_current_user_id();
$terms = array();
$user_id = $failed = $passed = 0;
$registration_enabled = false;
$registration_filter = function () use ( &$registration_enabled ) { return $registration_enabled; };
add_filter( 'option_users_can_register', $registration_filter );
add_filter( 'site_option_users_can_register', $registration_filter );
$assert = function ( $name, $condition ) use ( &$failed, &$passed ) {
	$condition ? ++$passed : ++$failed;
	echo ( $condition ? 'PASS ' : 'FAIL ' ) . $name . "\n";
};
try {
	register_taxonomy( 'bf_test_public', 'post', array( 'public' => true ) );
	register_taxonomy( 'bf_test_private', 'post', array( 'public' => false ) );
	foreach ( array( 'allowed', 'excluded', 'outside', 'private' ) as $name ) {
		$taxonomy = 'private' === $name ? 'bf_test_private' : 'bf_test_public';
		$term = wp_insert_term( 'BF security ' . $name . ' ' . wp_generate_uuid4(), $taxonomy );
		if ( is_wp_error( $term ) ) { throw new Exception( 'Unable to create term fixture' ); }
		$terms[ $name ] = array( 'id' => $term['term_id'], 'taxonomy' => $taxonomy );
	}
	$field = array(
		'slug' => 'terms', 'type' => 'taxonomy', 'taxonomy' => 'bf_test_public',
		'ajax' => array( 'ajax' ), 'taxonomy_order' => 'ASC',
		'taxonomy_include' => array( $terms['allowed']['id'], $terms['excluded']['id'] ),
		'taxonomy_exclude' => array( $terms['excluded']['id'] ),
	);
	$hidden_field = array_merge( $field, array( 'slug' => 'hidden-terms', 'hidden_field' => true ) );
	$text_field = array_merge( $field, array( 'slug' => 'text', 'type' => 'text' ) );
	$blocked_field = array_merge( $field, array( 'slug' => 'blocked-terms', 'taxonomy_exclude' => $field['taxonomy_include'] ) );
	$GLOBALS['buddyforms']['bf-security-public'] = array( 'public_submit' => 'public_submit', 'form_fields' => array( $field, $hidden_field, $text_field, $blocked_field ) );
	$GLOBALS['buddyforms']['bf-security-restricted'] = array( 'form_fields' => array( $field ) );
	$GLOBALS['buddyforms']['bf-security-legacy'] = array( 'public_submit' => 'public_submit', 'form_fields' => array( $field ) );
	$GLOBALS['buddyforms']['bf-security-registration'] = array( 'form_type' => 'registration', 'form_fields' => array( $field ) );
	$encoded_field = array_merge( $field, array( 'slug' => buddyforms_sanitize_slug( '分类' ) ) );
	$GLOBALS['buddyforms']['bf-security-encoded'] = array( 'public_submit' => 'public_submit', 'form_fields' => array( $encoded_field ) );
	wp_set_current_user( 0 );
	foreach ( array(
		'unknown form' => array( 'form_slug' => 'bf-security-missing' ),
		'restricted form' => array( 'form_slug' => 'bf-security-restricted' ),
		'unknown field' => array( 'field_slug' => 'missing' ),
		'missing field' => array( 'field_slug' => '' ),
		'private taxonomy' => array( 'taxonomy' => 'bf_test_private' ),
		'missing nonce' => array( 'nonce' => '' ),
		'invalid nonce' => array( 'nonce' => 'invalid' ),
		'array nonce' => array( 'nonce' => array( 'invalid' ) ),
		'array form' => array( 'form_slug' => array( 'invalid' ) ),
		'array field' => array( 'field_slug' => array( 'invalid' ) ),
		'array taxonomy' => array( 'taxonomy' => array( 'invalid' ) ),
		'hidden field' => array( 'field_slug' => 'hidden-terms' ),
		'non-taxonomy field' => array( 'field_slug' => 'text' ),
	) as $name => $request ) {
		$response = buddyforms_test_taxonomy_request( $request );
		$assert( 'guest cannot query ' . $name, isset( $response['success'] ) && false === $response['success'] );
	}
	$response = buddyforms_test_taxonomy_request( array( 'include' => '', 'exclude' => '', 'order' => 'DESC' ) );
	$ids = isset( $response['results'] ) ? array_map( 'intval', array_column( $response['results'], 'id' ) ) : array();
	$assert( 'public form honors server-configured term restrictions', array( (int) $terms['allowed']['id'] ) === $ids );
	$response = buddyforms_test_taxonomy_request( array( 'form_slug' => 'bf-security-legacy', 'field_slug' => '' ) );
	$ids = isset( $response['results'] ) ? array_map( 'intval', array_column( $response['results'], 'id' ) ) : array();
	$assert( 'legacy request resolves a single configured taxonomy field', array( (int) $terms['allowed']['id'] ) === $ids );
	$response = buddyforms_test_taxonomy_request( array( 'form_slug' => 'bf-security-encoded', 'field_slug' => $encoded_field['slug'] ) );
	$ids = isset( $response['results'] ) ? array_map( 'intval', array_column( $response['results'], 'id' ) ) : array();
	$assert( 'encoded non-ASCII field slug resolves its configured field', array( (int) $terms['allowed']['id'] ) === $ids );
	$response = buddyforms_test_taxonomy_request( array( 'form_slug' => 'bf-security-registration' ) );
	$assert( 'disabled registration form cannot be queried by a guest', isset( $response['success'] ) && false === $response['success'] );
	$registration_enabled = true;
	$response = buddyforms_test_taxonomy_request( array( 'form_slug' => 'bf-security-registration' ) );
	$ids = isset( $response['results'] ) ? array_map( 'intval', array_column( $response['results'], 'id' ) ) : array();
	$assert( 'enabled registration form remains accessible to guests', array( (int) $terms['allowed']['id'] ) === $ids );
	$response = buddyforms_test_taxonomy_request( array( 'field_slug' => 'blocked-terms', 'include' => $terms['outside']['id'], 'exclude' => '' ) );
	$assert( 'fully excluded include list stays empty', isset( $response['results'] ) && array() === $response['results'] );
	$response = buddyforms_test_taxonomy_request( array( 'search' => 'no-matching-security-term' ) );
	$assert( 'allowed search can return no terms', isset( $response['results'] ) && array() === $response['results'] );
	$user_id = wp_insert_user( array( 'user_login' => 'bf-security-' . wp_generate_uuid4(), 'user_pass' => wp_generate_password(), 'role' => 'subscriber' ) );
	if ( is_wp_error( $user_id ) ) { $user_id = 0; throw new Exception( 'Unable to create user fixture' ); }
	wp_set_current_user( $user_id );
	$response = buddyforms_test_taxonomy_request( array( 'form_slug' => 'bf-security-restricted' ) );
	$assert( 'subscriber cannot query restricted form', isset( $response['success'] ) && false === $response['success'] );
	foreach ( array( 'create', 'edit', 'draft', 'all' ) as $capability ) {
		$user = new WP_User( $user_id );
		$user->add_cap( 'buddyforms_bf-security-restricted_' . $capability );
		wp_set_current_user( 0 );
		wp_set_current_user( $user_id );
		$response = buddyforms_test_taxonomy_request( array( 'form_slug' => 'bf-security-restricted' ) );
		$ids = isset( $response['results'] ) ? array_map( 'intval', array_column( $response['results'], 'id' ) ) : array();
		$assert( 'authorized ' . $capability . ' user can search restricted form', array( (int) $terms['allowed']['id'] ) === $ids );
		$user->remove_cap( 'buddyforms_bf-security-restricted_' . $capability );
	}
} finally {
	foreach ( $terms as $term ) { wp_delete_term( $term['id'], $term['taxonomy'] ); }
	if ( $user_id ) { require_once ABSPATH . 'wp-admin/includes/user.php'; wp_delete_user( $user_id ); }
	unregister_taxonomy( 'bf_test_public' );
	unregister_taxonomy( 'bf_test_private' );
	$GLOBALS['buddyforms'] = $original_forms;
	wp_set_current_user( $original_user );
	remove_filter( 'option_users_can_register', $registration_filter );
	remove_filter( 'site_option_users_can_register', $registration_filter );
}
echo sprintf( "Results: %d passed, %d failed\n", $passed, $failed );
if ( $failed ) { throw new Exception( 'Taxonomy authorization regression failed' ); }
