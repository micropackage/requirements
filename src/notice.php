<?php
/**
 * Notice template
 *
 * @package micropackage/requirements
 */

use BracketSpace\Notification\Dependencies\Micropackage\Requirements\Requirements;

?>
<div class="error">
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				// Translators: Plugin name.
				__( 'The plugin: <strong>%s</strong> cannot be activated.', Requirements::$textdomain ),
				esc_html( $this->plugin_name )
			)
		);
		?>
	</p>

	<ul style="list-style: disc; padding-left: 20px;">
		<?php foreach ( $this->errors as $error ) : ?>
			<li><?php echo esc_html( $error ); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
