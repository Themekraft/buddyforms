<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var string $field_id */
/** @var string $default_option */
/** @var string $field_type */
/** @var integer $count */
/** @var array $field_options */
?>
<div class="element_field">
	<table id="field_<?php echo esc_attr( $field_id ); ?>" class="wp-list-table widefat posts element_field_table_sortable">
		<thead>
		<tr>
			<th colspan="2"><span style="padding-left: 10px;"><?php esc_html_e( 'Agreement', 'buddyforms' ); ?></span></th>
			<th><span style="padding-left: 10px;"><?php esc_html_e( 'Options', 'buddyforms' ); ?></span></th>
			<th class="manage-column column-author"><span style="padding-left: 10px;"><?php esc_html_e( 'Action', 'buddyforms' ); ?></span>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php if ( ! empty( $field_options ) ) : ?>
			<?php foreach ( $field_options as $key => $option ) : ?>
				<tr class="field_item field_item_<?php echo esc_attr( $field_id ); ?>_<?php echo esc_attr( $count ); ?>">
					<td><div class="dashicons dashicons-image-flip-vertical"></div></td>
					<td><p><b><?php esc_html_e( 'Agreement Text', 'buddyforms' ); ?></b></p>
										<?php
										$form_element = new Element_Textarea(
											'',
											'buddyforms_options[form_fields][' . $field_id . '][options][' . $key . '][label]',
											array(
												'value' => $option['label'],
												'cols'  => '50',
												'rows'  => '3',
											)
										);
										$form_element->render();
										?>
						<p><b><?php esc_html_e( 'Error Message', 'buddyforms' ); ?></b></p>
						<?php
						$error_message = empty( $option['error_message'] ) ? __( 'This field is Required', 'buddyforms' ) : $option['error_message'];
						$form_element  = new Element_Textarea(
							'',
							'buddyforms_options[form_fields][' . $field_id . '][options][' . $key . '][error_message]',
							array(
								'value' => $error_message,
								'cols'  => '50',
								'rows'  => '3',
							)
						);
						$form_element->render();
						?>
						</td>
					<td class="manage-column">
					<?php
						$value        = isset( $option['checked'] ) ? $option['checked'] : '';
						$form_element = new Element_Checkbox( '', 'buddyforms_options[form_fields][' . $field_id . '][options][' . $key . '][checked]', array( 'checked' => 'Checked' ), array( 'value' => $value ) );
						$form_element->render();

						$value        = isset( $option['required'] ) ? $option['required'] : '';
						$form_element = new Element_Checkbox( '', 'buddyforms_options[form_fields][' . $field_id . '][options][' . $key . '][required]', array( 'required' => 'Required' ), array( 'value' => $value ) );
						$form_element->render();
					?>
						</td>
					<td class="manage-column column-author">
						<a href="#" id="<?php echo esc_attr( $field_id ); ?>_<?php echo esc_attr( $count ); ?>" class="bf_delete_input" title="<?php esc_html_e( 'Delete', 'buddyforms' ); ?>"><?php esc_html_e( 'Delete', 'buddyforms' ); ?></a>
					</td>
				</tr>
				<?php $count ++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>

	<table class="wp-list-table widefat posts ">
		<tbody>
		<tr>
			<td>
				<select id="gdpr_option_type">
					<option value="none"><?php esc_html_e( 'Select a template', 'buddyforms' ); ?></option>
					<option value="registration"><?php esc_html_e( 'Registration', 'buddyforms' ); ?></option>
					<option value="contact"><?php esc_html_e( 'Contact Form', 'buddyforms' ); ?></option>
					<option value="post"><?php esc_html_e( 'Post Submission', 'buddyforms' ); ?></option>
					<option value="other"><?php esc_html_e( 'Other', 'buddyforms' ); ?></option>
				</select>
			</td>
			<td class="manage-column">
				<a href="#" data-gdpr-type="<?php echo esc_attr( $field_id ); ?>" class="button bf_add_gdpr">+</a>
			</td>
		</tr>
		</li></tbody>
	</table>

</div>

