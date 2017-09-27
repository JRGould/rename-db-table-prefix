<?php

/**
 * Run the actual updates and modifications
 *
 * @since      1.0.0
 *
 * @package    RDTP
 * @subpackage RDTP/includes
 */

/**
 * Run the actual updates and modifications
 *
 * Taken from https://github.com/iandunn/wp-cli-rename-db-prefix/
 *
 * @package    RDTP
 * @subpackage RDTP/includes
 * @author     Jeff Gould <jrgould@gmail.com>
 */
class RDTP_Prefix_Updater {

	/**
	 * The old table prefix
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $old_prefix    The old table prefix
	 */
	protected $old_prefix;

	/**
	 * The new table prefix
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $new_prefix    The new table prefix
	 */
	protected $new_prefix;

	/**
	 * Initialize class with new prefix.
	 *
	 * @since    1.0.0
	 * @param   string    $new_prefix    The new table prefix
	 */
	public function __construct( $new_prefix ) {
		global $wpdb;

		$this->wpdb = $wpdb;
		$this->new_prefix = $new_prefix;
		$this->old_prefix = $wpdb->base_prefix;

	}

	public function run() {
		$this->wpdb->show_errors( WP_DEBUG ); // This makes it easier to catch errors while developing this command, but we don't need to show them to users
		if ( is_multisite() ) {
			return new WP_Error( 'rdtp_multisite_err', __( "This plugin doesn't support MultiSite yet." ), 'rdtp' );
		}

		$progress = array();
		try {
			$progress[] = __( 'updating `wp-config.php`', 'rdtp' );
			$this->update_wp_config();

			$progress[] = __( 'renaming WordPress tables', 'rdtp' );
			$this->rename_wordpress_tables();

			$progress[] = __( 'updating blog options tables', 'rdtp' );
			$this->update_blog_options_tables();

			$progress[] = __( 'updating the options table', 'rdtp' );
			$this->update_options_table();

			$progress[] = __( 'updating the usermeta table', 'rdtp' );
			$this->update_usermeta_table();
			// TODO: set global $table_prefix to new one now, or earlier in process, to avoid errors during shutdown, etc?
			return true;
		} catch ( Exception $exception ) {
			// TODO: run reverse operations to attempt to undo errored actions
			return new WP_Error( 'rdtp_rename_failed', sprintf( __('Failed to rename DB Table Prefix. There was an error when %s. You may need to restore your `wp-config.php` file and your database from your backups.', 'rdtp'), array_pop( $progress ) ) );
		}
	}

	/**
	 * Update the prefix in `wp-config.php`
	 *
	 * @throws Exception
	 */
	protected function update_wp_config() {
		$wp_config_path     = $this->locate_wp_config();
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
	 * Locate wp-config.php
	 *
	 * @return bool|string location of wp-config.php file or false if not found
	 */
	public static function locate_wp_config() {
		static $path;
		if ( null === $path ) {
			if ( file_exists( ABSPATH . 'wp-config.php' ) )
				$path = ABSPATH . 'wp-config.php';
			elseif ( file_exists( ABSPATH . '../wp-config.php' ) && ! file_exists( ABSPATH . '/../wp-settings.php' ) )
				$path = ABSPATH . '../wp-config.php';
			else
				$path = false;
			if ( $path )
				$path = realpath( $path );
		}
		return $path;
	}

	/**
	 * Rename all of WordPress' database tables
	 *
	 * @throws Exception
	 */
	protected function rename_wordpress_tables() {
		$show_table_query = sprintf(
			'SHOW TABLES LIKE "%s%%";',
			$this->wpdb->esc_like( $this->old_prefix )
		);
		$tables = $this->wpdb->get_results( $show_table_query, ARRAY_N );
		if ( ! $tables ) {
			throw new Exception( 'MySQL error: ' . $this->wpdb->last_error );
		}
		foreach ( $tables as $table ) {
			$table = substr( $table[0], strlen( $this->old_prefix ) );
			$rename_query = sprintf(
				"RENAME TABLE `%s` TO `%s`;",
				$this->old_prefix . $table,
				$this->new_prefix . $table
			);
			if ( false === $this->wpdb->query( $rename_query ) ) {
				throw new Exception( 'MySQL error: ' . $this->wpdb->last_error );
			}
		}
	}
	/**
	 * Update rows in all of the site `options` tables
	 *
	 * @throws Exception
	 */
	protected function update_blog_options_tables() {
		if ( ! is_multisite() ) {
			return;
		}
		throw new Exception( 'Not done yet' );
		// todo this hasn't been tested at all
		// todo should this really go after update_options_table, and reuse the same query?
		// todo is this running on the root site twice b/c update_options_table() hits that too? should call either that or this, based on is_multisite() ?
		$sites = wp_get_sites( array( 'limit' => false ) );   //todo can't use b/c already renamed tables?
		//blogs = $this->wpdb->get_col( "SELECT blog_id FROM `" . $this->new_prefix . "blogs` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC" );
		if ( ! $sites ) {
			throw new Exception( 'Failed to get all sites.' );  // todo test
		}
		foreach ( $sites as $site ) {
			$update_query = $this->wpdb->prepare( "
				UPDATE `{$this->new_prefix}{$site->blog_id}_options`
				SET   option_name = %s
				WHERE option_name = %s
				LIMIT 1;",
				$this->new_prefix . $site->blog_id . '_user_roles',
				$this->old_prefix . $site->blog_id . '_user_roles'
			);
			if ( ! $this->wpdb->query( $update_query ) ) {
				throw new Exception( 'MySQL error: ' . $this->wpdb->last_error ); // todo test
			}
		}
	}
	/**
	 * Update rows in the `options` table
	 *
	 * @throws Exception
	 */
	protected function update_options_table() {
		$update_query = $this->wpdb->prepare( "
			UPDATE `{$this->new_prefix}options`
			SET   option_name = %s
			WHERE option_name = %s
			LIMIT 1;",
			$this->new_prefix . 'user_roles',
			$this->old_prefix . 'user_roles'
		);
		if ( ! $this->wpdb->query( $update_query ) ) {
			throw new Exception( 'MySQL error: ' . $this->wpdb->last_error );
		}
	}
	/**
	 * Update rows in the `usermeta` table
	 *
	 * @throws Exception
	 */
	protected function update_usermeta_table() {
		$rows = $this->wpdb->get_results( "SELECT meta_key FROM `{$this->new_prefix}usermeta`;" );
		if ( ! $rows ) {
			throw new Exception( 'MySQL error: ' . $this->wpdb->last_error );
		}
		foreach ( $rows as $row ) {
			$meta_key_prefix = substr( $row->meta_key, 0, strlen( $this->old_prefix ) );
			if ( $meta_key_prefix !== $this->old_prefix ) {
				continue;
			}
			$new_key = $this->new_prefix . substr( $row->meta_key, strlen( $this->old_prefix ) );
			$update_query = $this->wpdb->prepare( "
				UPDATE `{$this->new_prefix}usermeta`
				SET meta_key=%s
				WHERE meta_key=%s
				LIMIT 1;",
				$new_key,
				$row->meta_key
			);
			if ( ! $this->wpdb->query( $update_query ) ) {
				throw new Exception( 'MySQL error: ' . $this->wpdb->last_error );
			}
		}
	}
}
