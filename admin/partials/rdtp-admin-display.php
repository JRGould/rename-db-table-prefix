<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    RDTP
 * @subpackage RDTP/admin/partials
 */

global $wpdb;

?>

<div class="rdtp-wrap">
	<h1> <?php echo __( 'Rename DB Table Prefix', 'rdtp' );?></h1>
	<form method="post" action="" id="rdtp-form" >
		<input type="checkbox" name="confirm-db-backup" id="rdtp-confirm-db-backup">
		<input type="checkbox" name="confirm-config-backup" id="rdtp-confirm-config-backup">
		<?php wp_nonce_field( 'rdtp-rename-db-table-prefix' ); ?>
		Current Prefix: <strong id="rdtp-current-prefix"><?php echo $wpdb->base_prefix;?></strong><br>
		<label for="new-prefix">New Prefix:</label>
		<input type="text" name="new-prefix" value="<?php echo $wpdb->base_prefix;?>">
		<button type="submit" id="rdtp-submit" name="submit">Submit</button>
	</form>
</div>

	<!-- This file should primarily consist of HTML with a little bit of PHP. -->
