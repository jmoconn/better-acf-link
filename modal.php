<?php
	$div = $modal_args['div'];
	$value = $modal_args['value'];
	$field = $modal_args['field'];
	$sub_fields = $modal_args['sub_fields'];

	$label = $value['name'];
	if ( isset( $value['label'] ) && !empty( $value['label'] ) ) {
		$label = $value['label'];
	}
?>

<div <?php echo acf_esc_attrs( $div ); ?>>

	<?php acf_hidden_input( array( 'name' => $field['name'] ) );?>

	<div class="acfe-modal tm-custom-link" data-title="<?php echo $field['label']; ?>" data-size="medium" data-footer="<?php _e( 'Update', 'acfe' );?>">
		<div class="acfe-modal-wrapper">
			<div class="acfe-modal-content">

				<div class="acf-fields -top">

					<?php foreach ( $sub_fields as $sub_field ): ?>

					<?php acf_render_field_wrap( $sub_field );?>

					<?php endforeach;?>

				</div>

			</div>
		</div>
	</div>

	<a href="#" class="button" data-name="add" target=""><?php _e( 'Select Link', 'acf' );?></a>

	<div class="link-wrap">
		<span class="link-title"><?php echo esc_html( $label ); ?></span>
		<a class="link-url" href="<?php echo esc_url( $value['url'] ); ?>" target="_blank"><?php echo esc_html( $value['name'] ); ?></a>
		<i class="acf-icon -link-ext acf-js-tooltip" title="<?php _e( 'Opens in a new window/tab', 'acf' );?>"></i>
		<a class="acf-icon -pencil -clear acf-js-tooltip" data-name="edit" href="#" title="<?php _e( 'Edit', 'acf' );?>"></a>
		<a class="acf-icon -cancel -clear acf-js-tooltip" data-name="remove" href="#" title="<?php _e( 'Remove', 'acf' );?>"></a>
	</div>

</div>