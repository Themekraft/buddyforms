<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var int $size */
/** @var string[] $errors */
?>

<div class="bf-alert error is-dismissible">
	<strong class="alert-heading"><?php echo wp_kses( _n( 'The following error was found:', 'The following errors were found:', $size, 'buddyforms' ), buddyforms_wp_kses_allowed_atts() ); ?></strong>
	<ul style="padding: 0; padding-inline-start: 40px;">
		<li><?php echo wp_kses( $errors_string, buddyforms_wp_kses_allowed_atts() ); ?></li>
	</ul>
</div>
