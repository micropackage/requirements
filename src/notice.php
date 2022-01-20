<?php
/**
 * Notice template
 *
 * @package micropackage/requirements
 */

?>
<div class="error">
	<p><?php echo wp_kses_post( $message ); ?></p>

	<ul style="list-style: disc; padding-left: 20px;">
		<?php foreach ( $this->errors as $error ) : ?>
			<li><?php echo esc_html( $error ); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
