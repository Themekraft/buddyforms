<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The users submissions table
 *
 * This template can be overridden by copying it to yourtheme/buddyforms/the-table.php.
 */

ob_start();
require BUDDYFORMS_INCLUDES_PATH . 'resources/pfbc/Style/LoopStyle.php';
$css = ob_get_clean();
$table_css = buddyforms_minify_css( $css );
file_put_contents( dirname( BUDDYFORMS_INCLUDES_PATH ) . '/assets/css/bf-table-css-' . $form_slug . '.css', $table_css );
$form_table_css_url = BUDDYFORMS_ASSETS . 'css/bf-table-css-' . $form_slug . '.css';
wp_register_style( 'bf-table-css-' . $form_slug, $form_table_css_url );
wp_enqueue_style( 'bf-table-css-' . $form_slug );
?>

	<div id="buddyforms-table-view" class="buddyforms_posts_table buddyforms-posts-container">

		<?php if ( $the_lp_query->have_posts() ) : ?>

			<table class="table table-striped buddyforms-posts-content">
				<thead>
				<tr>
					<th class="created">
						<span><?php esc_html_e( 'Created', 'buddyforms' ); ?></span>
					</th>
					<th class="title">
						<?php if ( $buddyforms[ $form_slug ]['form_type'] === 'contact' ) : ?>
							<span><?php esc_html_e( 'Subject', 'buddyforms' ); ?></span>
						<?php elseif ( $buddyforms[ $form_slug ]['form_type'] === 'registration' ) : ?>
							<span><?php esc_html_e( 'Name', 'buddyforms' ); ?></span>
						<?php else : ?>
							<span><?php esc_html_e( 'Title', 'buddyforms' ); ?></span>
						<?php endif; ?>

					</th>
					<?php do_action( 'buddyforms_the_thead_th_after_title', get_the_ID(), $form_slug ); ?>
					<th class="status">
						<span><?php esc_html_e( 'Status', 'buddyforms' ); ?></span>
					</th>
					<?php if ( is_user_logged_in() && $buddyforms[ $form_slug ]['post_type'] != 'bf_submissions' ) { ?>
						<th class="actions">
							<span><?php esc_html_e( 'Actions', 'buddyforms' ); ?></span>
						</th>
					<?php } ?>
					<?php do_action( 'buddyforms_the_thead_tr_inner_last', get_the_ID(), $form_slug ); ?>
				</tr>
				</thead>
				<tbody>

				<?php
				while ( $the_lp_query->have_posts() ) :
					$the_lp_query->the_post();

					$form_slug = apply_filters( 'buddyforms_loop_form_slug', $form_slug, get_the_ID() );

					$the_permalink = get_permalink();

					$post_type = isset( $buddyforms[ $form_slug ]['post_type'] ) ? $buddyforms[ $form_slug ]['post_type'] : false;

					if ( $post_type === 'bf_submissions' ) {
						$the_permalink = '#';
					}

					$the_permalink = apply_filters( 'buddyforms_post_link_on_the_table', $the_permalink, get_the_ID(), $form_slug );

					$post_status = get_post_status();

					$the_title = get_the_title();

					if ( $the_title == 'none' ) {
						$the_subject = get_post_meta( get_the_id(), 'subject', true );

						if ( $the_subject ) {
							$the_title = $the_subject;
						}
					}

					$post_status_css  = buddyforms_get_post_status_css_class( $post_status, $form_slug );
					$post_status_name = buddyforms_get_post_status_readable( $post_status );
					$post_id          = get_the_ID();
					?>

					<tr id="bf_post_tr_<?php the_ID(); ?>" class="<?php echo esc_attr( $post_status_css ); ?>">
						<td class="bf_posts_<?php the_ID(); ?>">
							<?php
							// Create the modal for the submissions single view
							if ( $post_type === 'bf_submissions' ) {
								?>
								<div style="display:none;" id="bf-submission-modal_<?php the_ID(); ?>">
									<?php buddyforms_locate_template( 'submission-single', $form_slug ); ?>
								</div>
							<?php } ?>
							<span class="mobile-th"><?php esc_html_e( 'Created ', 'buddyforms' ); ?></span>
							<?php the_time( 'F j, Y' ); ?>
						</td>
						<td>
<!--                            <span class="mobile-th">--><?php // _e( 'Title', 'buddyforms' ); ?><!--</span>-->
							<a class="<?php echo $post_type === 'bf_submissions' ? 'bf-submission-modal' : ''; ?> "
							   data-id="<?php the_ID(); ?>" href="<?php echo esc_attr( $the_permalink ); ?>" rel="bookmark"
							   title="<?php esc_html_e( 'Permanent Link to', 'buddyforms' ); ?> <?php the_title_attribute(); ?>"><?php echo wp_kses( $the_title, buddyforms_wp_kses_allowed_atts() ); ?></a>
							<?php do_action( 'buddyforms_the_loop_item_title_after', get_the_ID() ); ?>
						</td>
						<?php do_action( 'buddyforms_the_table_td_after_title_last', get_the_ID(), $form_slug ); ?>
						<td colspan="2" class="table-wrapper">
							<table class="table table-inner">
								<tbody>
								<tr class="<?php echo esc_attr( $post_status_css ); ?>">
									<td>
										<span class="mobile-th"><?php esc_html_e( 'Status', 'buddyforms' ); ?></span>
										<div class="status-item">
											<div class="table-item-status"><?php echo wp_kses( $post_status_name, buddyforms_wp_kses_allowed_atts() ); ?></div>
										</div>
									</td>
									<td>
										<div class="meta">
											<span class="mobile-th"><?php esc_html_e( 'Actions', 'buddyforms' ); ?></span>
											<?php buddyforms_post_entry_actions( $form_slug ); ?>
											<?php do_action( 'buddyforms_the_loop_after_actions', get_the_ID(), $form_slug ); ?>
										</div>
									</td>
								</tr>
								<?php do_action( 'buddyforms_the_table_inner_tr_last', get_the_ID() ); ?>
								</tbody>
							</table>
						</td>
						<?php do_action( 'buddyforms_the_table_tr_last', get_the_ID(), $form_slug ); ?>
					</tr>

					<?php do_action( 'buddyforms_after_loop_item', get_the_ID(), $form_slug ); ?>

				<?php endwhile; ?>

				</tbody>
			</table>

			<div class="navigation">
				<?php if ( function_exists( 'wp_pagenavi' ) ) : ?>
					<?php wp_pagenavi(); ?>

					<?php
				else :
					$next_posts_link    = get_next_posts_link( '&larr;' . __( 'Previous Entries', 'buddyforms' ), $the_lp_query->max_num_pages );
					$previos_posts_link = get_previous_posts_link( __( 'Next Entries', 'buddyforms' ) . '&rarr;' );
					?>
					<div class="alignright"><?php echo wp_kses( apply_filters( 'buddyforms_previos_posts_link', $previos_posts_link, $form_slug ), buddyforms_wp_kses_allowed_atts() ); ?></div>
					<div class="alignleft"><?php echo wp_kses( apply_filters( 'buddyforms_next_posts_link', $next_posts_link, $form_slug ), buddyforms_wp_kses_allowed_atts() ); ?></div>
				<?php endif; ?>

			</div>

		<?php else : ?>

			<div id="message" class="info">
				<p><?php echo wp_kses( $empty_post_message, buddyforms_wp_kses_allowed_atts() ); ?></p>
			</div>

		<?php endif; ?>

		<div class="bf_modal">
			<div style="display: none;"><?php wp_editor( '', 'buddyforms_form_content' ); ?></div>
		</div>

	</div>
<?php
wp_reset_query();
