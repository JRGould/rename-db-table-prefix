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

<div class="rdtp-wrap wrap">
	<?php error_log( print_r( [ 'test' ], 1 ) );?>
	<h1> <?php echo __( 'Rename DB Table Prefix', 'rdtp' );?></h1>
	<form method="post" action="" id="rdtp-form" >
		<?php wp_nonce_field( 'rdtp-rename-db-table-prefix', 'rdtp-rename-db-table-prefix' ); ?>

		<div id="rdtp-step1" class="rdtp-step active">
			<p>
				<strong class="title" >Have you backed up your database?</strong>
				<label class="radio"><input type="radio" name="confirm-db-backup" id="rdtp-confirm-db-backup" value="true"> <span>Yes.</span></label>
				<label class="radio checked bad"><input type="radio" name="confirm-db-backup" id="rdtp-confirm-db-backup" value="false" checked disabled> <span>No.</span></label>
			</p>
		</div>

		<div id="rdtp-step2" class="rdtp-step">
			<p>
				<strong class="title" >Have you backed up your wp-config.php file?</strong>
				<label class="radio"><input type="radio" name="confirm-config-backup" id="rdtp-confirm-config-backup" value="true" > <span>Yes.</span></label>
				<label class="radio"><input type="radio" name="confirm-config-backup" id="rdtp-perform-config-backup" value="false" > <span>No, please do it for me.</span></label>
				<?php wp_nonce_field( 'rdtp-backup-wp-config', 'rdtp-backup-wp-config', false ); ?>
			</p>
		</div>

		<div id="rdtp-step3" class="rdtp-step">
			Current Prefix: <strong id="rdtp-current-prefix"><?php echo $wpdb->base_prefix;?></strong><br>
			<label for="new-prefix">New Prefix:</label>
			<input type="text" name="new-prefix" value="<?php echo $wpdb->base_prefix . uniqid() . '_';?>">
			<button type="submit" id="rdtp-submit" name="submit">Submit</button>
		</div>

	</form>
</div>

	<!-- This file should primarily consist of HTML with a little bit of PHP. -->
