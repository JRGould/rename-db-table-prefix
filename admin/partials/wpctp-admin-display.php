<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WPCTP
 * @subpackage WPCTP/admin/partials
 */

global $wpdb;

?>

<div class="wpctp-wrap">
	<h1> <?php echo __( 'Change Table Prefix', 'wpctp' );?></h1>
	<form method="post" action="" id="wpctpForm" >
		<?php wp_nonce_field( 'wpctp-change-table-prefix' ); ?>
		Current Prefix: <strong><?php echo $wpdb->base_prefix;?></strong><br>
		<label for="new-prefix">New Prefix:</label>
		<input type="text" name="new_prefix" value="<?php echo $wpdb->base_prefix;?>">
		<button type="submit" id="wpctpSubmit" name="submit">Submit</button>
	</form>
</div>

	<!-- This file should primarily consist of HTML with a little bit of PHP. -->
