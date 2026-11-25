<?php
/**
 * Run on a disposable site with BuddyForms active:
 * wp eval-file tests/security/array-field-values-xss.php
 * A guest submits a public contact form with an array-shaped value (field[]=...) for fields that use
 * the generic sanitizer (hidden) and for a text field. Then it checks what was stored, that legitimate array fields
 * (checkboxes, multiple select) still save, and that the admin Submissions list prints stored values,
 * including raw ones saved by older versions, as text.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

if ( ! empty( $args ) ) {
	$case = json_decode( base64_decode( $args[0] ), true );
	wp_set_current_user( 0 );
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_SERVER['REQUEST_URI']    = '/';
	// What wp_magic_quotes() does to a real request.
	$_POST             = wp_slash( $case['post'] );
	$_POST['_wpnonce'] = wp_create_nonce( 'buddyforms_form_nonce' );
	add_filter( 'pre_wp_mail', '__return_true' );
	$result = buddyforms_process_submission( $_POST );
	echo wp_json_encode( array( 'hasError' => ! empty( $result['hasError'] ), 'error' => isset( $result['error_message'] ) ? $result['error_message'] : ( $result['hasError'] ? wp_json_encode( ErrorHandler::get_instance()->get_global_error() ) : '' ) ) );
	return;
}

$_SERVER['HTTP_HOST'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : wp_parse_url( home_url(), PHP_URL_HOST );
$posts   = array();
$passed  = $failed = 0;
$marker  = 'bfav' . wp_rand( 100000, 999999 );
$payload = '<img src=x onerror=alert(1)><script>window.' . $marker . '=1</script>' . $marker;
$fields  = array(
	'ref'    => array( 'type' => 'hidden', 'slug' => 'bf_ref', 'name' => 'Ref', 'value' => 'default' ),
	'name'   => array( 'type' => 'text', 'slug' => 'bf_name', 'name' => 'Name' ),
	'note'   => array( 'type' => 'hidden', 'slug' => 'bf_note', 'name' => 'Note', 'value' => '' ),
	'colors' => array( 'type' => 'checkbox', 'slug' => 'bf_colors', 'name' => 'Colors', 'options' => array( 1 => array( 'label' => 'Red', 'value' => 'red' ), 2 => array( 'label' => 'Light blue', 'value' => 'light blue' ) ) ),
	'sizes'  => array( 'type' => 'dropdown', 'slug' => 'bf_sizes', 'name' => 'Sizes', 'multiple' => array( 'multiple' ), 'options' => array( 1 => array( 'label' => 'S', 'value' => 's' ), 2 => array( 'label' => 'M & L', 'value' => 'M & L' ) ) ),
	'gdpr'   => array( 'type' => 'gdpr', 'slug' => 'bf_gdpr', 'name' => 'GDPR', 'description' => '', 'options' => array( 1 => array( 'label' => 'I agree' ) ) ),
);
// Markup in the rendered column: tags or event handlers inside real tags.
$no_markup = function ( $html ) use ( $marker ) {
	return false === stripos( $html, '<script' ) && false === stripos( $html, '<img' ) && ! preg_match( '/<[a-z][^>]*\son[a-z]+\s*=/i', $html ) && false !== strpos( $html, $marker );
};
$column = function ( $entry_id, $slug, $form_slug ) {
	$_GET['form_slug'] = $form_slug;
	$table             = new BuddyForms_Submissions_List_Table();
	ob_start();
	$table->column_default( get_post( $entry_id ), $slug );
	$html = ob_get_clean();
	unset( $_GET['form_slug'] );
	return $html;
};

try {
	if ( ! class_exists( 'BuddyForms_Submissions_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		require_once BUDDYFORMS_INCLUDES_PATH . 'admin/submissions.php';
	}
	set_current_screen( 'buddyforms_page_buddyforms_submissions' );

	$admin = wp_insert_user( array( 'user_login' => 'bfav-admin-' . $marker, 'user_email' => 'admin-' . $marker . '@example.com', 'user_pass' => wp_generate_password(), 'role' => 'administrator' ) );
	if ( is_wp_error( $admin ) ) {
		throw new Exception( 'Unable to create user fixture' );
	}
	$posts['form'] = wp_insert_post( array( 'post_type' => 'buddyforms', 'post_status' => 'publish', 'post_title' => 'BF fixture contact ' . wp_generate_uuid4(), 'post_author' => $admin ), true );
	if ( is_wp_error( $posts['form'] ) ) {
		throw new Exception( 'Unable to create form fixture' );
	}
	$slug = get_post_field( 'post_name', $posts['form'] );
	update_post_meta( $posts['form'], '_buddyforms_options', array( 'name' => 'contact', 'slug' => $slug, 'singular_name' => 'contact', 'status' => 'publish', 'comment_status' => 'closed', 'form_type' => 'contact', 'post_type' => 'bf_submissions', 'public_submit' => 'public_submit', 'form_fields' => $fields ) );
	$posts['draft'] = wp_insert_post( array( 'post_type' => 'bf_submissions', 'post_status' => 'auto-draft', 'post_title' => 'Auto Draft' ) );
	buddyforms_regenerate_global_options();
	// The global is read once at boot and this process renders the list itself.
	$GLOBALS['buddyforms'] = get_option( 'buddyforms_forms' );

	$submission = array(
		'form_slug'    => $slug,
		'post_id'      => $posts['draft'],
		'form_action'  => 'create',
		'bf_post_type' => 'bf_submissions',
		'bf_hweb'      => '',
		'redirect_to'  => '/',
		'bf_ref'       => array( $payload, array( 'nested' => $payload ) ),
		'bf_name'      => array( 'Ann ' . $payload ),
		'bf_note'     => '<b>555</b> 0100',
		'bf_colors'    => array( 'red', 'light blue' ),
		'bf_sizes'     => array( 's', 'M & L' ),
		'bf_gdpr_1'    => 'checked',
	);
	$result = WP_CLI::launch_self( 'eval-file', array( __FILE__, base64_encode( wp_json_encode( array( 'post' => $submission ) ) ) ), array(), false, true );
	wp_cache_flush();
	if ( 0 !== $result->return_code || false === strpos( $result->stdout, '"hasError":false' ) ) {
		WP_CLI::line( '     ' . trim( $result->stdout . ' ' . $result->stderr ) );
	}
	$entry = get_posts( array( 'post_type' => 'bf_submissions', 'post_status' => 'any', 'numberposts' => 1, 'meta_key' => '_bf_form_slug', 'meta_value' => $slug ) );
	$entry = $entry ? $entry[0]->ID : 0;
	if ( $entry ) {
		$posts['entry'] = $entry;
	}
	// An entry saved by an older version, with the raw markup still in its meta.
	$posts['legacy'] = wp_insert_post( array( 'post_type' => 'bf_submissions', 'post_status' => 'publish', 'post_title' => 'BF legacy ' . $marker, 'post_author' => $admin ) );
	update_post_meta( $posts['legacy'], '_bf_form_slug', $slug );
	update_post_meta( $posts['legacy'], 'bf_ref', array( $payload ) );
	update_post_meta( $posts['legacy'], 'bf_name', $payload );
	update_post_meta( $posts['legacy'], 'bf_note', array( 'a' => $payload, 'b' => array( $payload ) ) );

	$cases = array(
		'guest submission is saved (precondition)' => function () use ( $entry ) {
			return (bool) $entry;
		},
		'array value of a hidden field is stored as plain text' => function () use ( $entry, $marker ) {
			$value = get_post_meta( $entry, 'bf_ref', true );
			return is_array( $value ) && $marker === $value[0] && array( 'nested' => $marker ) === $value[1];
		},
		'array value of a text field is dropped by its own sanitizer' => function () use ( $entry ) {
			return '' === get_post_meta( $entry, 'bf_name', true );
		},
		'single value keeps the scalar sanitizer' => function () use ( $entry ) {
			return '555 0100' === get_post_meta( $entry, 'bf_note', true );
		},
		'checkbox values are saved' => function () use ( $entry ) {
			return array( 'red', 'light blue' ) === get_post_meta( $entry, 'bf_colors', true );
		},
		'multiple select values are saved' => function () use ( $entry ) {
			return array( 's', 'M & L' ) === get_post_meta( $entry, 'bf_sizes', true );
		},
		'list shows the submitted values' => function () use ( $column, $entry, $slug, $marker ) {
			return '555 0100' === $column( $entry, 'bf_note', $slug ) && 'redlight blue' === $column( $entry, 'bf_colors', $slug ) && 'sM &amp; L' === $column( $entry, 'bf_sizes', $slug );
		},
		'list keeps the GDPR agreement markup' => function () use ( $column, $entry, $slug ) {
			return '<p>I agree <strong>(Checked)</strong></p>' === $column( $entry, 'bf_gdpr', $slug );
		},
		'list prints a raw stored array as text' => function () use ( $column, $no_markup, $slug, $posts, $marker ) {
			$html = $column( $posts['legacy'], 'bf_ref', $slug );
			return $no_markup( $html ) && false !== strpos( $html, '&lt;script&gt;window.' . $marker );
		},
		'list prints a raw stored string as text' => function () use ( $column, $no_markup, $slug, $posts, $marker ) {
			$html = $column( $posts['legacy'], 'bf_name', $slug );
			return $no_markup( $html ) && false !== strpos( $html, '&lt;img src=x onerror=alert(1)&gt;' );
		},
		'list prints a raw stored nested array as text' => function () use ( $column, $no_markup, $slug, $posts ) {
			return $no_markup( $column( $posts['legacy'], 'bf_note', $slug ) );
		},
		'author column escapes the nickname' => function () use ( $column, $no_markup, $slug, $posts, $admin, $payload ) {
			update_user_meta( $admin, 'nickname', $payload );
			$html = $column( $posts['legacy'], 'Author', $slug );
			return $no_markup( $html );
		},
	);

	foreach ( $cases as $name => $check ) {
		$ok = (bool) $check();
		$ok ? ++$passed : ++$failed;
		WP_CLI::line( ( $ok ? 'PASS ' : 'FAIL ' ) . $name );
	}
} finally {
	foreach ( $posts as $id ) {
		wp_delete_post( $id, true );
	}
	require_once ABSPATH . 'wp-admin/includes/user.php';
	if ( ! empty( $admin ) && ! is_wp_error( $admin ) ) {
		wp_delete_user( $admin );
	}
	buddyforms_regenerate_global_options();
}
WP_CLI::line( sprintf( 'Results: %d passed, %d failed', $passed, $failed ) );
if ( $failed ) {
	throw new Exception( 'Array field values XSS regression failed' );
}
