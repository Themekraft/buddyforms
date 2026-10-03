<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$css_form_id = 'buddyforms_form_' . $form_slug;
?>
<style data-target="global" type="text/css" <?php echo wp_kses( apply_filters( 'buddyforms_add_global_style_attributes', '', $css_form_id ), buddyforms_wp_kses_allowed_atts() ); ?>>
	.bf-show-login-form {
		padding: 0.5em;
	}

	.bf-show-login-form h3 {
		margin-top: 0.5em;
	}

	.bf-show-login-form input[type="text"], .bf-show-login-form input[type="password"] {
		display: block;
	}

	.bf-show-login-form .bf-login-error {
		border-left: 4px solid #d63638;
		padding: 12px;
		margin-left: 0;
		margin-bottom: 20px;
		background-color: #fff;
		box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);
	}

	.bf-show-login-form #bf_loginform .input {
		width: 100%;
		background: #fff;
		border: 1px solid #ccc;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		color: inherit;
		font: inherit;
		font-size: 15px;
		padding: 15px;
		min-height: 40px;
	}
</style>
