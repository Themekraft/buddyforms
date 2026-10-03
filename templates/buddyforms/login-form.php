<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Auth form login
 *
 * This template can be overridden by copying it to yourtheme/buddyforms/form-login.php.
 */

if ( is_user_logged_in() ) {
	return;
}

function buddyforms_toogle_wp_login_form() {
	?>
		<script>
			jQuery(document).ready(function () {
				jQuery(document).on("click", '.bf-show-login', function () {
					jQuery('.bf-show-login-form').toggle();
				});
			});
		</script>
	<?php
}
add_action( 'wp_footer', 'buddyforms_toogle_wp_login_form' );

?>

<div class="buddyforms-info"><?php esc_html_e( 'Returning user?', 'buddyforms' ); ?>
	<a href="#" class="bf-show-login"><?php esc_html_e( 'Click here to login', 'buddyforms' ); ?></a>
</div>
<?php buddyforms_wp_login_form( true, $form_slug ); ?>
