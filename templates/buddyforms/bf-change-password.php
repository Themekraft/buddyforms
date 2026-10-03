<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var string $current_url */
?>
<?php if ( isset( $_GET['bf-password-reset'] ) && $_GET['bf-password-reset'] == 'true' ) : ?>
	<div class="bf-alert success">
		<span><?php esc_html_e( 'Password changed successfully', 'buddyforms' ); ?></span>
	</div>
<?php endif; ?>

<form id="buddyforms_form_password_reset" method="POST" action="<?php echo esc_attr( $current_url ); ?>">
	<fieldset>
		<p>
			<label for="buddyforms_user_pass"><?php esc_html_e( 'New Password', 'buddyforms' ); ?></label>
			<input name="buddyforms_user_pass" id="buddyforms_user_pass" class="required" type="password"/>
		</p>
		<p>
			<label for="buddyforms_user_pass_confirm"><?php esc_html_e( 'Password Confirm', 'buddyforms' ); ?></label>
			<input name="buddyforms_user_pass_confirm" id="buddyforms_user_pass_confirm" class="required" type="password"/>
		</p>
		<p>
			<input type="hidden" name="buddyforms_action" value="reset-password"/>
			<input type="hidden" name="buddyforms_redirect" value="<?php echo esc_attr( $redirect ); ?>"/>
			<input type="hidden" name="buddyforms_password_nonce" value="<?php echo wp_create_nonce( 'buddyforms-password-nonce' ); ?>"/>

		<div style="margin: 1em;">
			<div id="password-strength"></div>
		</div>

		<input id="buddyforms_password_submit" type="submit" data-target="buddyforms_form_password_reset" class="bf-submit" value="<?php esc_html_e( 'Change Password', 'buddyforms' ); ?>"/>

		</p>
	</fieldset>
</form>
