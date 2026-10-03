<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $buddyforms, $form_slug, $post_id;
?>

<script>
	jQuery(document).ready(function () {
		jQuery(".bf_submit_form<?php echo esc_js( $post_id ); ?> :input").attr("disabled", true);
		jQuery(".bf_submit_form<?php echo esc_js( $post_id ); ?>").show();
	});
</script>
<div id="poststuff" class="bf_submit_form<?php echo esc_attr( $post_id ); ?>">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<div class="buddyforms-metabox postbox-submissions postbox">
				<h3 class="hndle"><span><?php esc_html_e( 'Entry', 'buddyforms' ); ?></span></h3>
				<div class="inside">
					<script>
						jQuery(document).ready(function () {
							jQuery("#metabox_<?php echo esc_js( $form_slug ); ?> :input").attr("disabled", true);
							jQuery('#metabox_<?php echo esc_js( $form_slug ); ?>').prop('readonly', true);
							jQuery('#metabox_<?php echo esc_js( $form_slug ); ?>').find('input, textarea, button, select').attr('disabled', 'disabled');
						});
					</script>
					<?php
					// Create the form object
					$form = new Form( 'submissions_' . $form_slug );

					// Set the form attribute
					$form->configure(
						array(
							// "prevent" => array("bootstrap", "jQuery", "focus"),
							// "action" => $redirect_to,
							'view'  => new View_Metabox(),
							'class' => 'standard-form',
						)
					);

					$fields = $buddyforms[ $form_slug ]['form_fields'];

					$args = array(
						'post_type'    => $buddyforms[ $form_slug ]['post_type'],
						'customfields' => $fields,
						'post_id'      => $post_id,
						'form_slug'    => $form_slug,
					);

					// if the form has custom field to save as post meta data they get displayed here
					buddyforms_form_elements( $form, $args );

					$form->render();

					?>
				</div>
			</div>


		</div>
		<div id="postbox-container-1" class="buddyforms-metabox postbox-container">
			<div id="submitdiv" class="buddyforms-metabox postbox">

				<h3 class="hndle"><span><?php esc_html_e( 'Entry Actions', 'buddyforms' ); ?></span></h3>
				<div class="inside">
					<div class="submitbox">
						<div id="minor-publishing-<?php echo esc_attr( $post_id ); ?>" class="frm_remove_border">
							<div class="misc-pub-section">
								<div class="clear"></div>
							</div>
							<div id="misc-publishing-actions-<?php echo esc_attr( $post_id ); ?>">

								<div class="misc-pub-section curtime misc-pub-curtime">
											<span id="timestamp-<?php echo esc_attr( $post_id ); ?>">
											Submitted: <b><?php echo get_the_date( 'l, F j, Y', $post_id ); ?></b>    </span>
								</div>

								<div class="misc-pub-section">
									<span class="dashicons dashicons-format-aside wp-media-buttons-icon"></span>&nbsp;<a
											href="#" onclick="window.print();return false;"><?php esc_html_e( 'Print', 'buddyforms' ); ?></a>
								</div>

								<div class="misc-pub-section">
									<a href="?post_type=buddyforms&page=buddyforms_submissions&form_slug=<?php echo esc_attr( $form_slug ); ?>"
									   class="button button-primary bf-close-submissions-modal"
									   data-id="<?php the_ID(); ?>"><?php esc_html_e( 'Back', 'buddyforms' ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="buddyforms-metabox postbox frm_with_icons">
				<h3 class="hndle"><span><?php esc_html_e( 'Entry Details', 'buddyforms' ); ?></span></h3>
				<div class="inside">

					<div class="misc-pub-section">
						<span class="dashicons dashicons-id wp-media-buttons-icon"></span>
						<?php esc_html_e( 'Entry ID', 'buddyforms' ); ?>:
						<b><?php echo esc_html( $post_id ); ?></b>
					</div>

				</div>
			</div>

			<?php

			if ( is_admin() ) {
				$bf_registration_user_id = get_post_meta( $post_id, '_bf_registration_user_id', true );

				if ( $bf_registration_user_id ) {
					?>
					<div class="buddyforms-metabox postbox">
						<h3 class="hndle"><span><?php esc_html_e( 'WordPress User', 'buddyforms' ); ?></span></h3>
						<div class="inside">
							<div class="misc-pub-section">
								<p><?php esc_html_e( 'User ID:', 'buddyforms' ); ?> <?php echo esc_html( $bf_registration_user_id ); ?></p>
								<p>
									<span class="dashicons dashicons-admin-users"></span>&nbsp;
									<a href="<?php echo esc_attr( get_edit_user_link( $bf_registration_user_id ) ); ?>"><?php esc_html_e( 'Edit User', 'buddyforms' ); ?></a>
								</p>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php
			}


			if ( buddyforms_core_fs()->is__premium_only() ) {
				if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
					if ( is_admin() ) {
						$user_data = get_post_meta( $post_id, '_bf_user_data', true );

						if ( $user_data ) {
							?>
							<div class="buddyforms-metabox postbox">
								<h3 class="hndle"><span><?php esc_html_e( 'User Information', 'buddyforms' ); ?></span></h3>
								<div class="inside">

									<?php foreach ( $user_data as $uinfo => $uval ) { ?>
										<div class="misc-pub-section">
											<?php echo esc_html( $uinfo ); ?>:
											<b><?php echo esc_html( $uval ); ?></b>
										</div>
									<?php } ?>

								</div>
							</div>
						<?php } ?>
						<?php
					}
				}
			}
			if ( buddyforms_core_fs()->is_not_paying() && ! buddyforms_core_fs()->is_trial() ) {
				?>
				<div class="buddyforms-metabox postbox">
					<h3 class="hndle"><span><?php esc_html_e( 'Get all insights about your user', 'buddyforms' ); ?></span></h3>
					<div class="inside">
						<?php
						buddyforms_go_pro(
							'',
							'',
							array(
								__( 'IP Address', 'buddyforms' ),
								__( 'Referer', 'buddyforms' ),
								__( 'Browser', 'buddyforms' ),
								__( 'Platform', 'buddyforms' ),
								__( 'Reports', 'buddyforms' ),
								__( 'User Agent', 'buddyforms' ),
							)
						);
						?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
