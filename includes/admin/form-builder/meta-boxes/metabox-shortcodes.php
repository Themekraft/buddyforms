<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function buddyforms_metabox_shortcodes() {
	global $post;

	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true ); ?>

	<div class="bf-shortcode">
		<div id="bf-shortcode">

			<?php do_action( 'buddyforms_shortcode_list_before' ); ?>

			<div class="bf-tile">
				<label for="the-form"><?php esc_html_e( 'Display Form', 'buddyforms' ); ?></label>
				<div class="tooltip">
					<input id="the-form" type="text" class="bf-ready-to-copy" readonly="readonly" onfocus="this.select();" onmouseup="return false;" value='[bf form_slug="<?php echo esc_attr( $post->post_name ); ?>"]'>
					<span class="tooltip-container"><?php esc_html_e( 'Copy to clipboard', 'buddyforms' ); ?></span>
				</div>
				<p class="description"><?php esc_html_e( 'Display the form.', 'buddyforms' ); ?></p>
			</div>

			<?php if ( $buddyform['form_type'] == 'post' ) { ?>

				<div class="bf-tile">
					<label for="post-list"><?php esc_html_e( 'User Posts List', 'buddyforms' ); ?></label>
					<div class="tooltip">
						<input id="post-list" type="text" class="bf-ready-to-copy" readonly="readonly" onfocus="this.select();" onmouseup="return false;" value='[bf_user_posts_list form_slug="<?php echo esc_attr( $post->post_name ); ?>"]'>
						<span class="tooltip-container"><?php esc_html_e( 'Copy to clipboard', 'buddyforms' ); ?></span>
					</div>
					<p class="description"><?php esc_html_e( 'Display the logged in users posts or a login form.', 'buddyforms' ); ?></p>
				</div>

				<?php if ( isset( $buddyform['attached_page'] ) && $buddyform['attached_page'] != 'none' ) { ?>

					<div class="bf-row">
						<div class="bf-tile alt">
							<label for="link-to-form"><?php esc_html_e( 'Link to Form', 'buddyforms' ); ?></label>
							<div class="tooltip">
								<input id="link-to-form" type="text" class="bf-ready-to-copy" readonly="readonly" onfocus="this.select();" onmouseup="return false;" value='[bf_link_to_form form_slug="<?php echo esc_attr( $post->post_name ); ?>"]'>
								<span class="tooltip-container"><?php esc_html_e( 'Copy to clipboard', 'buddyforms' ); ?></span>
							</div>
							<p class="description"><?php esc_html_e( 'Display a link to the form.', 'buddyforms' ); ?></p>
						</div>

						<div class="bf-tile alt">
							<label for="link-to-posts"><?php esc_html_e( 'Link to Users Posts', 'buddyforms' ); ?></label>
							<div class="tooltip">
								<input id="link-to-posts" type="text" class="bf-ready-to-copy" readonly="readonly" onfocus="this.select();" onmouseup="return false;" value='[bf_link_to_user_posts form_slug="<?php echo esc_attr( $post->post_name ); ?>"]'>
								<span class="tooltip-container"><?php esc_html_e( 'Copy to clipboard', 'buddyforms' ); ?></span>
							</div>
							<p class="description"><?php esc_html_e( 'Display a link to the logged in users post.', 'buddyforms' ); ?></p>
						</div>
					</div>


				<?php } ?>
			<?php } ?>

			<?php do_action( 'buddyforms_shortcode_list_after' ); ?>

		</div>
		<br>
		<p>
			<a target="_blank" href="http://docs.buddyforms.com/article/141-shortcodes"><?php esc_html_e( 'List of all Available Shortcodes', 'buddyforms' ); ?> </a>
		</p>
	</div>
	<?php
}
