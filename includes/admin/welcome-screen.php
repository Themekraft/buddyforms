<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//
// Add the Settings Page to the BuddyForms Menu
//
function buddyforms_welcome_screen_menu() {
	add_submenu_page( 'edit.php?post_type=buddyforms', __( 'Welcome', 'buddyforms' ), __( 'Welcome', 'buddyforms' ), 'manage_options', 'buddyforms_welcome_screen', 'buddyforms_welcome_screen_content', 1 );
}

add_action( 'admin_menu', 'buddyforms_welcome_screen_menu', 9999 );

function buddyforms_welcome_screen_content() {
	?>
	<div id="bf_admin_wrap" class="wrap">

		<div class="wrap about-wrap buddyforms-welcome">
			<h1><?php esc_html_e( 'Welcome to BuddyForms', 'buddyforms' ); ?> <?php echo wp_kses( BUDDYFORMS_VERSION, buddyforms_wp_kses_allowed_atts() ); ?></h1>
		</div>
		<div class="welcome-screen-separator"></div>
		<div class="wrapper">
			<div class="bf-welcome-accordion active">
				<div class="bf-welcome-accordion_tab">
					Introduction
					<div class="bf-welcome-accordion_arrow">
						<img src="<?php echo esc_url( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/PJRz0Fc.png" alt="arrow">
					</div>
				</div>
				<div class="bf-welcome-accordion_content">
				<iframe id="bf-welcome-video-youtube" src="https://www.youtube.com/embed/DoPLWBBlRvA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
			
			<div class="bf-welcome-accordion">
				<div class="bf-welcome-accordion_tab">
					Templates
					<div class="bf-welcome-accordion_arrow">
						<img src="<?php echo esc_url( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/PJRz0Fc.png" alt="arrow">
					</div>
				</div>
				<div class="bf-welcome-accordion_content bf-welcome-accordion-templates">
					<?php
						$buddyforms_templates   = buddyforms_form_builder_register_templates();
						$none_dependency_string = __( 'None', 'buddyforms' );
					if ( empty( $is_wizard ) && isset( $_REQUEST['bf_template'] ) ) {
						$is_wizard = true;
					}
					?>
					<div id="buddyforms_template_list_container">

						<?php foreach ( $buddyforms_templates as $sort_key => $sort_item ) { ?>

							<h2><?php echo wp_kses( strtoupper( $sort_key ), buddyforms_wp_kses_allowed_atts() ); ?>&nbsp;<?php esc_html_e( 'FORMS', 'buddyforms' ); ?></h2>

							<?php
							foreach ( $sort_item as $key => $template ) {

								$dependencies = buddyforms_form_builder_template_get_dependencies( $template );

								$disabled = $dependencies != $none_dependency_string ? 'disabled' : '';

								?>
								<div class="bf-3-tile bf-tile 
								<?php
								if ( $dependencies != $none_dependency_string ) {
									echo 'disabled ';
								}
								?>
								">
									<h4 class="bf-tile-title"><?php echo wp_kses( $template['title'], buddyforms_wp_kses_allowed_atts() ); ?></h4>
									<div class="xbf-col-50 bf-tile-desc-wrap">
										<p class="bf-tile-desc"><?php echo wp_kses( wp_trim_words( $template['desc'], 15 ), buddyforms_wp_kses_allowed_atts() ); ?></p>
									</div>
									<div class="bf-tile-preview-wrap"></div>
									<?php if ( $dependencies != $none_dependency_string ) { ?>
										<p class="bf-tile-dependencies"><?php esc_html_e( 'Dependencies: ', 'buddyforms' ); ?><?php echo wp_kses( $dependencies, buddyforms_wp_kses_allowed_atts() ); ?></p>
									<?php } else { ?>
										<button <?php echo esc_attr( $disabled ); ?> id="btn-add-new-<?php echo esc_attr( $key ); ?>"
																		data-type="<?php echo esc_attr( $sort_key ); ?>"
																		data-template="<?php echo esc_attr( $key ); ?>"
																		class="welcome-screen-template btn btn-primary btn-50"
																		onclick="document.location.href='<?php echo esc_attr( self_admin_url( 'post-new.php?post_type=buddyforms&template=' . $key ) ); ?>'">
											<?php esc_html_e( 'Use This Template', 'buddyforms' ); ?>
										</button>
									<?php } ?>
									<div id="template-<?php echo esc_attr( $key ); ?>" style="display:none;">
										<div class="bf-tile-desc-wrap">
											<p class="bf-tile-desc"><?php echo wp_kses( $template['desc'], buddyforms_wp_kses_allowed_atts() ); ?></p>
											<button <?php echo esc_attr( $disabled ); ?> id="btn-add-new-<?php echo esc_attr( $key ); ?>"
																			data-type="<?php echo esc_attr( $sort_key ); ?>"
																			data-template="<?php echo esc_attr( $key ); ?>"
																			class="welcome-screen-template button button-primary"
																			onclick="document.location.href='<?php echo esc_attr( self_admin_url( 'post-new.php?post_type=buddyforms&template=' . $key ) ); ?>'">
												<!-- <span class="dashicons dashicons-plus"></span>  -->
												<?php esc_html_e( 'Use This Template', 'buddyforms' ); ?>
											</button>
										</div>
										<iframe id="iframe-<?php echo esc_attr( $key ); ?>" width="100%" height="800px" scrolling="yes"
												frameborder="0" class="bf-frame"
												style="background: transparent; height: 639px; height: 75vh; margin: 0 auto; padding: 0 5px; width: calc( 100% - 10px );"></iframe>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			</div>

			<div class="bf-welcome-accordion">
				<div class="bf-welcome-accordion_tab">
					Gutenberg Support
					<div class="bf-welcome-accordion_arrow">
						<img src="<?php echo esc_url( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/PJRz0Fc.png" alt="arrow">
					</div>
				</div>
				<div class="bf-welcome-accordion_content">
					<div class="bf-welcome-accordion_item">
						<p class="item_title">Embed Forms</p>
						<p>Embed any BuddyForms Form as Gutenberg Block. Just select the form you like to embed in the block sidebar.</p>
						<img style="width:650px;" src="<?php echo esc_attr( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/gutenberg-form.gif" alt="">
					</div>

					<div class="bf-welcome-accordion_item">
						<p class="item_title">List Submissions</p>
						<p>You can list form submissions form any form and post type. Filter post lists by author or only display posts from the logged in user. Use the options in the Block sidebar.</p>
						<img style="width:650px;" src="<?php echo esc_attr( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/gutenberg-list-submissions.gif" alt="">
					</div>

					<div class="bf-welcome-accordion_item">
						<p class="item_title">Embed Navigation</p>
						<p>Link to form endpoints or user posts lists for every post form with an attached page to create and edit submissions. You can select the attached page under the "Edit Submissions" tab in the Form Builder.</p>
						<img style="width:650px;" src="<?php echo esc_attr( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/gutenberg-add-navigation.gif" alt="">
					</div>

					<div class="bf-welcome-accordion_item">
						<p class="item_title">Login/ Logout Form</p>
						<p>Display a login form or a logout button if the user is logged in.</p>
						<img style="width:650px;" src="<?php echo esc_attr( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/gutenberg-login-form.gif" alt="">
					</div>
					
				</div>
			</div>

			<div class="bf-welcome-accordion">
				<div class="bf-welcome-accordion_tab">
					Shortcodes
					<div class="bf-welcome-accordion_arrow">
						<img src="<?php echo esc_url( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/PJRz0Fc.png" alt="arrow">
					</div>
				</div>
				<div class="bf-welcome-accordion_content">
					<div class="bf-welcome-accordion_item">
						<p class="item_title">Display a Form</p>
						<p class="item_title_description">Use this shortcode if you wanna show a form on frontend. Don't forget to change YOUR-FORM-SLUG to your own form slug.</p>
						<p class="bf-shortcode-doc">[bf form_slug="YOUR-FORM-SLUG"]</p>
					</div>
					<div class="bf-welcome-accordion_item">
						<p class="item_title">Display Submissions</p>
						<p class="item_title_description">Use this shortcode if you wannan show a list of entries belongs to a Form. Don't forget to change YOUR-FORM-SLUG to your own form slug. The attribute "list_posts_style" is optional and its possible values ​​are "table" or "list" (default). </p>
						<p class="bf-shortcode-doc">[bf_posts_list form_slug="YOUR-FORM-SLUG" list_posts_style=""]</p>
					</div>
					<div class="bf-welcome-accordion_item">
						<p class="item_title">Link to Form</p>
						<p class="item_title_description">This shortcode will create a link to the form for creating or editing submissions. Don't forget to change YOUR-FORM-SLUG to your own form slug. The attribute "label" is optional (default value is "Add New").</p>
						<p class="bf-shortcode-doc">[bf_link_to_form form_slug="YOUR-FORM-SLUG" label=""]</p>
					</div>
					<div class="bf-welcome-accordion_item">
						<p class="item_title">Link to User Posts</p>
						<p class="item_title_description">For logged in users you can use the following shortcode to display their submissions. Don't forget to change YOUR-FORM-SLUG to your own form slug. The attribute "label" is optional (default value is "View").</p>
						<p class="bf-shortcode-doc">[bf_link_to_user_posts form_slug="YOUR-FORM-SLUG" label=""]</p>
					</div>
					<div class="bf-welcome-accordion_item">
						<p class="item_title">User Posts List</p>
						<p class="item_title_description">For logged in users you can use the following shortcode to display a the list of posts. Don't forget to change YOUR-FORM-SLUG to your own form slug.</p>
						<p class="bf-shortcode-doc">[bf_user_posts_list form_slug="YOUR-FORM-SLUG"]</p>
					</div>
				</div>
			</div>
			
			<div class="bf-welcome-accordion">
				<div class="bf-welcome-accordion_tab">
					More Info
					<div class="bf-welcome-accordion_arrow">
						<img src="<?php echo esc_url( BUDDYFORMS_ASSETS ); ?>admin/img/welcome-screen/PJRz0Fc.png" alt="arrow">
					</div>
				</div>
				<div class="bf-welcome-accordion_content">
					<div class="bf-welcome-accordion_item">
						<p class="item_title">Documentation</p>
						<p>Our goal is to help you, that's why if you have any questions or concerns, on our website you can find all the information related to BuddyForms.</p>
						<a class="documentation_link" href="https://docs.buddyforms.com/" target="_blank">Visit Now!</a>
					</div>
					
				</div>
			</div>

		</div>

	</div>
	<?php
}

add_action( 'admin_head', 'buddyforms_welcome_scren_templates_redirect' );
function buddyforms_welcome_scren_templates_redirect() {
	if ( is_admin() ) {
		if ( isset( $_GET['template'] ) ) {
			$template = sanitize_text_field( wp_unslash( $_GET['template'] ) );
			?>
				<script>
					jQuery( document ).ready(function() {
						jQuery("#btn-compile-<?php echo esc_js( $template ); ?>").click();
					});
				</script>
			<?php
		}
	}
}
