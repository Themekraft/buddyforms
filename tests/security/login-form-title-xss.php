<?php
/**
 * Run on a disposable site with BuddyForms active:
 * wp eval-file tests/security/login-form-title-xss.php
 * A guest submits a post through a public form whose content holds [bf_login_form] with markup
 * written as C escapes (\x3c): wp_kses_post() sees no tag, shortcode_parse_atts() turns them back
 * into "<". Then it checks the rendered post and that legitimate output still renders.
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
	echo wp_json_encode( array( 'hasError' => ! empty( $result['hasError'] ) ) );
	return;
}

$_SERVER['HTTP_HOST'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : wp_parse_url( home_url(), PHP_URL_HOST );
$posts  = array();
$previous_registration = get_option( 'users_can_register' );
$passed = $failed = 0;
$marker = 'bflx' . wp_rand( 100000, 999999 );
$evil   = 'https://x.test/' . $marker . '.js';
// Stored form of the payload: "<" and ">" as C escapes.
$escape = function ( $html ) {
	return str_replace( array( '<', '>' ), array( '\x3c', '\x3e' ), $html );
};
$title_payload = $escape( '<script src=' . $evil . '></script><a href=# onclick=alert(1)>' . $marker . '</a>' );
$content       = '[bf_login_form title="' . $title_payload . '"]';
$fields        = array(
	'title'   => array( 'type' => 'title', 'slug' => 'buddyforms_form_title', 'name' => 'Title' ),
	'content' => array( 'type' => 'content', 'slug' => 'buddyforms_form_content', 'name' => 'Content' ),
);
$captcha       = array( 'type' => 'captcha', 'slug' => 'captcha', 'name' => 'Captcha', 'captcha_site_key' => 'site-' . $marker, 'captcha_private_key' => 'private' );
// Render inside a post, like a real page: some add-ons (Custom Login) check get_the_ID() in the_content.
$render        = function ( $html, $post_id = 0 ) use ( &$posts ) {
	global $post;
	$previous = $post;
	$post     = get_post( $post_id ? $post_id : $posts['page'] );
	setup_postdata( $post );
	$output = apply_filters( 'the_content', $html );
	$post   = $previous;
	if ( $previous ) {
		setup_postdata( $previous );
	}
	return $output;
};
$heading       = function ( $html ) {
	return preg_match( '#<h3>(.*?)</h3>#s', $html, $m ) ? $m[1] : null;
};
$no_script     = function ( $html ) use ( $evil ) {
	// Event handlers inside real tags; escaped text may still spell them out.
	return false === stripos( $html, '<script' ) && ! preg_match( '/<[a-z][^>]*\son[a-z]+\s*=/i', $html ) && false === strpos( $html, $evil . '"' );
};

try {
	// The registration form only renders when the site allows registration.
	update_option( 'users_can_register', 1 );
	$admin = wp_insert_user( array( 'user_login' => 'bflx-admin-' . $marker, 'user_email' => 'admin-' . $marker . '@example.com', 'user_pass' => wp_generate_password(), 'role' => 'administrator' ) );
	if ( is_wp_error( $admin ) ) {
		throw new Exception( 'Unable to create user fixture' );
	}
	$posts['page'] = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'BF page ' . $marker, 'post_author' => $admin ) );
	$form_options  = array(
		'public'  => array( 'form_type' => 'post', 'post_type' => 'post', 'public_submit' => 'public_submit', 'attached_page' => $posts['page'], 'form_fields' => $fields ),
		'captcha' => array( 'form_type' => 'post', 'post_type' => 'post', 'public_submit' => 'public_submit', 'form_fields' => array_merge( $fields, array( 'captcha' => $captcha ) ) ),
		'reg'     => array( 'form_type' => 'registration', 'post_type' => 'bf_submissions', 'registration' => array( 'new_user_role' => 'subscriber' ), 'form_fields' => array( 'captcha' => $captcha ) ),
	);
	$slugs = array();
	foreach ( $form_options as $name => $options ) {
		$id = wp_insert_post( array( 'post_type' => 'buddyforms', 'post_status' => 'publish', 'post_title' => 'BF fixture ' . $name . ' ' . wp_generate_uuid4(), 'post_author' => $admin ), true );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Unable to create form fixture' );
		}
		$posts[ $name ] = $id;
		$slugs[ $name ] = get_post_field( 'post_name', $id );
		update_post_meta( $id, '_buddyforms_options', array_merge( array( 'name' => $name, 'slug' => $slugs[ $name ], 'singular_name' => $name, 'status' => 'publish', 'comment_status' => 'closed' ), $options ) );
	}
	// A form whose login form is replaced by the registration form (its "logged in only" setting).
	$posts['gated'] = wp_insert_post( array( 'post_type' => 'buddyforms', 'post_status' => 'publish', 'post_title' => 'BF fixture gated ' . wp_generate_uuid4(), 'post_author' => $admin ) );
	$slugs['gated'] = get_post_field( 'post_name', $posts['gated'] );
	update_post_meta( $posts['gated'], '_buddyforms_options', array( 'name' => 'gated', 'slug' => $slugs['gated'], 'singular_name' => 'gated', 'status' => 'publish', 'form_type' => 'post', 'post_type' => 'post', 'public_submit' => 'registration_form', 'logged_in_only_reg_form' => $slugs['reg'], 'form_fields' => $fields ) );
	$posts['draft'] = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'auto-draft', 'post_title' => 'Auto Draft' ) );
	buddyforms_regenerate_global_options();
	// The global is read once at boot and this process renders the forms itself.
	$GLOBALS['buddyforms'] = get_option( 'buddyforms_forms' );

	$submission = array( 'form_slug' => $slugs['public'], 'post_id' => $posts['draft'], 'form_action' => 'create', 'bf_post_type' => 'post', 'bf_hweb' => '', 'redirect_to' => '/', 'buddyforms_form_title' => 'BF entry ' . $marker, 'buddyforms_form_content' => wp_slash( $content ) );
	$result     = WP_CLI::launch_self( 'eval-file', array( __FILE__, base64_encode( wp_json_encode( array( 'post' => $submission ) ) ) ), array(), false, true );
	wp_cache_flush();
	$entry = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 's' => 'BF entry ' . $marker, 'numberposts' => 1 ) );
	$entry = $entry ? $entry[0] : null;
	if ( $entry ) {
		$posts['entry'] = $entry->ID;
	}
	if ( 0 !== $result->return_code ) {
		WP_CLI::line( '     ' . trim( $result->stderr ) );
	}
	$entry_html = $entry ? $render( $entry->post_content, $entry->ID ) : '';

	$cases = array(
		'guest submission publishes the escaped payload (precondition)' => function () use ( $entry, $title_payload ) {
			return $entry && false !== strpos( $entry->post_content, $title_payload );
		},
		'published entry prints no script or event handler' => function () use ( $entry_html, $no_script ) {
			return '' !== $entry_html && $no_script( $entry_html );
		},
		'published entry shows the title markup as plain text' => function () use ( $entry_html, $heading, $evil, $marker ) {
			return '&lt;script src=' . $evil . '&gt;&lt;/script&gt;&lt;a href=# onclick=alert(1)&gt;' . $marker . '&lt;/a&gt;' === $heading( $entry_html );
		},
		'raw markup in the title is escaped' => function () use ( $render, $heading, $evil ) {
			$html = buddyforms_view_login_form( array( 'title' => '<script src=' . $evil . '></script><b>Hi</b>' ) );
			return '&lt;script src=' . $evil . '&gt;&lt;/script&gt;&lt;b&gt;Hi&lt;/b&gt;' === $heading( $html );
		},
		'block attribute title is escaped' => function () use ( $heading, $evil ) {
			if ( ! function_exists( 'buddyforms_block_render_login_form' ) ) {
				require_once BUDDYFORMS_INCLUDES_PATH . 'gutenberg/shortcodes/shortcodes-to-blocks.php';
			}
			$html = buddyforms_block_render_login_form( array( 'bf_title' => '<script src=' . $evil . '></script>Hi' ) );
			return '&lt;script src=' . $evil . '&gt;&lt;/script&gt;Hi' === $heading( $html );
		},
		'default login form renders unchanged' => function () use ( $render, $heading ) {
			$html = $render( '[bf_login_form]' );
			foreach ( array( 'id="bf_loginform"', 'name="log"', 'name="pwd"', 'name="rememberme"', 'name="redirect_to"', 'Username or Email Address' ) as $needle ) {
				if ( false === strpos( $html, $needle ) ) {
					return false;
				}
			}
			return 'Login' === $heading( $html );
		},
		'custom title, labels and redirect render' => function () use ( $render, $heading ) {
			$html = $render( '[bf_login_form title="Members & friends" label_username="Your user" label_log_in="Enter" redirect_url="https://example.com/after"]' );
			return 'Members &amp; friends' === $heading( $html ) && false !== strpos( $html, 'Your user' ) && false !== strpos( $html, 'value="Enter"' ) && false !== strpos( $html, 'value="https://example.com/after"' );
		},
		'login error messages render as text with the bold prefix' => function () use ( $render ) {
			$_GET = array( 'bf_login_error_redirect' => '1', 'error_msg_1' => 'Error: wrong <b>password</b>' );
			$html = $render( '[bf_login_form]' );
			$_GET = array();
			return false !== strpos( $html, '<strong>Error: </strong>wrong &lt;b&gt;password&lt;/b&gt;' );
		},
		'logged-in visitors get the logout link' => function () use ( $render, $admin ) {
			wp_set_current_user( $admin );
			$html = $render( '[bf_login_form]' );
			wp_set_current_user( 0 );
			return 1 === preg_match( '#<a href="[^"]*wp-login\.php\?action=logout[^"]*">Logout</a>#', $html );
		},
		'navigation labels keep markup but lose scripts and handlers' => function () use ( $render, $slugs, $escape, $evil, $no_script ) {
			$html = $render( '[buddyforms_nav form_slug="' . $slugs['public'] . '" label_view="' . $escape( '<i class=dashicons></i> View' ) . '" label_add="' . $escape( '<img src=x onerror=alert(1)><script src=' . $evil . '></script>Add' ) . '"]' );
			return $no_script( $html ) && false !== strpos( $html, '<i class="dashicons"></i> View' ) && false !== strpos( $html, 'Add</a>' ) && false !== strpos( $html, '/view/' . $slugs['public'] . '/' );
		},
		'submission link escapes its name and keeps link and target' => function () use ( $render, $escape ) {
			$html = $render( '[bf_new_submission_link link="https://example.com/new" name="' . $escape( '<b>Go</b>' ) . '" target="_self"]' );
			return false !== strpos( $html, '<a href="https://example.com/new" target="_self">&lt;b&gt;Go&lt;/b&gt;</a>' );
		},
		'public form with reCAPTCHA still prints its script' => function () use ( $render, $slugs ) {
			$html = $render( '[bf form_slug="' . $slugs['captcha'] . '"]' );
			return false !== strpos( $html, 'recaptcha/api.js' );
		},
		'registration form shown instead of the login form keeps its reCAPTCHA script' => function () use ( $render, $slugs, $marker ) {
			$html = $render( '[bf_login_form form_slug="' . $slugs['gated'] . '"]' );
			return false !== strpos( $html, 'recaptcha/api.js' ) && false !== strpos( $html, 'site-' . $marker );
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
	update_option( 'users_can_register', $previous_registration );
	buddyforms_regenerate_global_options();
}
WP_CLI::line( sprintf( 'Results: %d passed, %d failed', $passed, $failed ) );
if ( $failed ) {
	throw new Exception( 'Login form title XSS regression failed' );
}
