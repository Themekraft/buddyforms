<?php
/**
 * Run on a disposable site with BuddyForms active:
 * wp eval-file tests/security/template-path-traversal.php
 * Renders the List Submissions block and the template loader with traversal
 * slugs pointing at a probe file in uploads, and checks it never runs.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI || ! function_exists( 'buddyforms_locate_template' ) ) {
	exit;
}

$marker  = 'BF_TEMPLATE_PROBE_' . wp_generate_password( 12, false );
$uploads = wp_upload_dir();
$probe   = trailingslashit( $uploads['basedir'] ) . 'bf-template-probe-' . wp_generate_password( 8, false ) . '.php';
$passed  = $failed = 0;
$form    = 'bf-traversal-' . strtolower( wp_generate_password( 8, false ) );
$user    = 0;

// Relative path from the plugin's templates/buddyforms/ folder to the probe, without .php.
$from      = explode( '/', trim( wp_normalize_path( realpath( BUDDYFORMS_TEMPLATE_PATH . 'buddyforms' ) ), '/' ) );
$to        = explode( '/', trim( wp_normalize_path( dirname( $probe ) ), '/' ) );
$common    = 0;
while ( isset( $from[ $common ], $to[ $common ] ) && $from[ $common ] === $to[ $common ] ) {
	++$common;
}
$traversal = str_repeat( '../', count( $from ) - $common ) . implode( '/', array_slice( $to, $common ) ) . '/' . basename( $probe, '.php' );
$absolute  = str_repeat( '../', count( $from ) + 2 ) . ltrim( wp_normalize_path( dirname( $probe ) ), '/' ) . '/' . basename( $probe, '.php' );

$check = function ( $name, $ok ) use ( &$passed, &$failed ) {
	$ok ? ++$passed : ++$failed;
	WP_CLI::line( ( $ok ? 'PASS ' : 'FAIL ' ) . $name );
};
$render = function ( $callback ) {
	ob_start();
	try {
		$returned = call_user_func( $callback );
	} catch ( Throwable $error ) {
		$returned = '';
	}
	return ob_get_clean() . ( is_string( $returned ) ? $returned : '' );
};

try {
	if ( false === file_put_contents( $probe, "<?php echo '" . $marker . "';\n" ) ) {
		throw new Exception( 'Unable to write the probe file' );
	}
	$user = wp_insert_user( array( 'user_login' => 'bf-traversal-' . wp_generate_uuid4(), 'user_pass' => wp_generate_password(), 'role' => 'contributor' ) );
	if ( is_wp_error( $user ) ) {
		throw new Exception( 'Unable to create user fixture' );
	}
	wp_set_current_user( $user );

	global $buddyforms;
	$buddyforms[ $form ] = array( 'slug' => $form, 'name' => 'Traversal fixture', 'post_type' => 'post', 'form_type' => 'post', 'list_posts_style' => 'list', 'form_fields' => array() );

	$check( 'probe file runs when included directly', false !== strpos( $render( function () use ( $probe ) { include $probe; } ), $marker ) );

	foreach ( array( 'relative' => $traversal, 'absolute' => $absolute ) as $kind => $slug ) {
		$block = function () use ( $form, $slug ) {
			return buddyforms_block_list_submissions( array( 'bf_form_slug' => $form, 'bf_rights' => 'public', 'bf_list_posts_style' => $slug ) );
		};
		$check( "block with $kind traversal style does not include the probe", false === strpos( $render( $block ), $marker ) );
		$check( "template loader rejects $kind traversal slug", false === strpos( $render( function () use ( $slug, $form ) { buddyforms_locate_template( $slug, $form ); } ), $marker ) );
		$check( "shortcode with $kind traversal style does not include the probe", false === strpos( $render( function () use ( $form, $slug ) { return buddyforms_the_loop( array( 'form_slug' => $form, 'list_posts_style' => $slug ) ); } ), $marker ) );
	}
	foreach ( array( 'backslash' => 'the-loop\\..\\x', 'null byte' => "the-loop\0", 'array' => array( 'the-loop' ), 'empty' => '' ) as $name => $slug ) {
		$check( "template loader rejects $name slug", '' === $render( function () use ( $slug, $form ) { buddyforms_locate_template( $slug, $form ); } ) );
	}

	$table = $render( function () use ( $form ) { return buddyforms_block_list_submissions( array( 'bf_form_slug' => $form, 'bf_rights' => 'public', 'bf_list_posts_style' => 'table' ) ); } );
	$check( 'block still renders the table style', '' !== trim( $table ) && false === strpos( $table, $marker ) );

	// Add-ons register extra styles through both filters (Frontend Table adds data-table).
	$grant = function ( $styles ) { return array_merge( $styles, array( 'probe-style' ) ); };
	$path  = function ( $template_path, $slug ) use ( $probe ) { return 'probe-style' === $slug ? $probe : $template_path; };
	add_filter( 'buddyforms_granted_list_post_style', $grant );
	add_filter( 'buddyforms_locate_template', $path, 10, 2 );
	$check( 'block still renders a style granted by an add-on', false !== strpos( $render( function () use ( $form ) { return buddyforms_block_list_submissions( array( 'bf_form_slug' => $form, 'bf_rights' => 'public', 'bf_list_posts_style' => 'probe-style' ) ); } ), $marker ) );
	remove_filter( 'buddyforms_granted_list_post_style', $grant );
	$check( 'block ignores a style no add-on granted', false === strpos( $render( function () use ( $form ) { return buddyforms_block_list_submissions( array( 'bf_form_slug' => $form, 'bf_rights' => 'public', 'bf_list_posts_style' => 'probe-style' ) ); } ), $marker ) );
	remove_filter( 'buddyforms_locate_template', $path, 10 );
} finally {
	unset( $GLOBALS['buddyforms'][ $form ] );
	if ( file_exists( $probe ) ) {
		unlink( $probe );
	}
	if ( $user && ! is_wp_error( $user ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user( $user );
	}
}
WP_CLI::line( sprintf( 'Results: %d passed, %d failed', $passed, $failed ) );
if ( $failed ) {
	throw new Exception( 'Template path traversal regression failed' );
}
