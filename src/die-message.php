<?php
/**
 * `wp_die` message template
 *
 * @package micropackage/requirements
 */

?>
<h1><?php echo wp_kses_post( $message ); ?></h1>

<ul>
	<?php foreach ( $this->errors as $error ) : ?>
		<li><?php echo esc_html( $error ); ?></li>
	<?php endforeach; ?>
</ul>
