<?php

/**
 * Run the actual updates and modifications
 *
 * @since      1.0.0
 *
 * @package    WPCTP
 * @subpackage WPCTP/includes
 */

/**
 * Run the actual updates and modifications
 *
 * Taken from https://github.com/iandunn/wp-cli-rename-db-prefix/
 *
 * @package    WPCTP
 * @subpackage WPCTP/includes
 * @author     Jeff Gould <jrgould@gmail.com>
 */
class WPCTP_Prefix_Updater {
	/**
	 * Update the prefix in `wp-config.php`
	 *
	 * @throws Exception
	 */
	protected function update_wp_config() {
		if ( $this->is_dry_run ) {
			return;
		}
		$wp_config_path     = \WP_CLI\Utils\locate_wp_config(); // we know this is valid, because wp-cli won't run if it's not
		$wp_config_contents = file_get_contents( $wp_config_path );
		$search_pattern     = '/(\$table_prefix\s*=\s*)([\'"]).+?\\2(\s*;)/';
		$replace_pattern    = "\${1}'{$this->new_prefix}'\${3}";
		$wp_config_contents = preg_replace( $search_pattern, $replace_pattern, $wp_config_contents, -1, $number_replacements );
		if ( 0 === $number_replacements ) {
			throw new Exception( "Failed to replace `\$table_prefix` in `wp-config.php`." );
		}
		if ( ! file_put_contents( $wp_config_path, $wp_config_contents ) ) {
			throw new Exception( "Failed to update updated `wp-config.php` file." );
		}
	}
	/**
	 * Rename all of WordPress' database tables
	 *
	 * @throws Exception
	 */
	protected function rename_wordpress_tables() {
		global $wpdb;
		$show_table_query = sprintf(
			'SHOW TABLES LIKE "%s%%";',
			$wpdb->esc_like( $this->old_prefix )
		);
		$tables = $wpdb->get_results( $show_table_query, ARRAY_N );
		if ( ! $tables ) {
			throw new Exception( 'MySQL error: ' . $wpdb->last_error );
		}
		foreach ( $tables as $table ) {
			$table = substr( $table[0], strlen( $this->old_prefix ) );
			$rename_query = sprintf(
				"RENAME TABLE `%s` TO `%s`;",
				$this->old_prefix . $table,
				$this->new_prefix . $table
			);
			if ( $this->is_dry_run ) {
				\WP_CLI::line( $rename_query );
				continue;
			}
			if ( false === $wpdb->query( $rename_query ) ) {
				throw new Exception( 'MySQL error: ' . $wpdb->last_error );
			}
		}
	}
	/**
	 * Update rows in all of the site `options` tables
	 *
	 * @throws Exception
	 */
	protected function update_blog_options_tables() {
		global $wpdb;
		if ( ! is_multisite() ) {
			return;
		}
		throw new Exception( 'Not done yet' );
		// todo this hasn't been tested at all
		// todo should this really go after update_options_table, and reuse the same query?
		// todo is this running on the root site twice b/c update_options_table() hits that too? should call either that or this, based on is_multisite() ?
		$sites = wp_get_sites( array( 'limit' => false ) );   //todo can't use b/c already renamed tables?
		//blogs = $wpdb->get_col( "SELECT blog_id FROM `" . $this->new_prefix . "blogs` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC" );
		if ( ! $sites ) {
			throw new Exception( 'Failed to get all sites.' );  // todo test
		}
		foreach ( $sites as $site ) {
			$update_query = $wpdb->prepare( "
				UPDATE `{$this->new_prefix}{$site->blog_id}_options`
				SET   option_name = %s
				WHERE option_name = %s
				LIMIT 1;",
				$this->new_prefix . $site->blog_id . '_user_roles',
				$this->old_prefix . $site->blog_id . '_user_roles'
			);
			if ( $this->is_dry_run ) {
				\WP_CLI::line( $update_query );
				continue;
			}
			if ( ! $wpdb->query( $update_query ) ) {
				throw new Exception( 'MySQL error: ' . $wpdb->last_error ); // todo test
			}
		}
	}
	/**
	 * Update rows in the `options` table
	 *
	 * @throws Exception
	 */
	protected function update_options_table() {
		global $wpdb;
		$update_query = $wpdb->prepare( "
			UPDATE `{$this->new_prefix}options`
			SET   option_name = %s
			WHERE option_name = %s
			LIMIT 1;",
			$this->new_prefix . 'user_roles',
			$this->old_prefix . 'user_roles'
		);
		if ( $this->is_dry_run ) {
			\WP_CLI::line( $update_query );
			return;
		}
		if ( ! $wpdb->query( $update_query ) ) {
			throw new Exception( 'MySQL error: ' . $wpdb->last_error );
		}
	}
	/**
	 * Update rows in the `usermeta` table
	 *
	 * @throws Exception
	 */
	protected function update_usermeta_table() {
		global $wpdb;
		if ( $this->is_dry_run ) {
			$rows = $wpdb->get_results( "SELECT meta_key FROM `{$this->old_prefix}usermeta`;" );
		} else {
			$rows = $wpdb->get_results( "SELECT meta_key FROM `{$this->new_prefix}usermeta`;" );
		}
		if ( ! $rows ) {
			throw new Exception( 'MySQL error: ' . $wpdb->last_error );
		}
		foreach ( $rows as $row ) {
			$meta_key_prefix = substr( $row->meta_key, 0, strlen( $this->old_prefix ) );
			if ( $meta_key_prefix !== $this->old_prefix ) {
				continue;
			}
			$new_key = $this->new_prefix . substr( $row->meta_key, strlen( $this->old_prefix ) );
			$update_query = $wpdb->prepare( "
				UPDATE `{$this->new_prefix}usermeta`
				SET meta_key=%s
				WHERE meta_key=%s
				LIMIT 1;",
				$new_key,
				$row->meta_key
			);
			if ( $this->is_dry_run ) {
				\WP_CLI::line( $update_query );
				continue;
			}
			if ( ! $wpdb->query( $update_query ) ) {
				throw new Exception( 'MySQL error: ' . $wpdb->last_error );
			}
		}
	}
}
