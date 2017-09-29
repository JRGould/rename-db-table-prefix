=== Plugin Name ===
Contributors: jrgould, deliciousbrains
Tags: database, prefix, table_prefix, mysql
Requires at least: 3.0.1
Tested up to: 4.8.2
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Rename DB Table Prefix does what it says on the tin.

== Description ==

Need to change your table prefix from `wp_` to something else on a site that's already running? Not running a multisite install? Rename DB Table Prefix can probably help.

RDTP's primary functionality is based on the great WP-CLI package, [wp-cli-rename-db-prefix](https://github.com/iandunn/wp-cli-rename-db-prefix).

Use at your own risk and make backups before running this plugin - it is entirely possible that this could break your site and you will need to be able to restore your database and `wp-config.php` files from good backups if this happens.

== Installation ==

1. Upload `rdtp.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Find `Rename DB Table Prefix` in the Tools menu
1. Back up your database and `wp-config.php` files before using Rename DB Table Prefix

== Frequently Asked Questions ==

= Why would I need to rename my table prefix =

Primarily for situations when you have dev or staging sites that have different table prefixes than your prod site. When running migrations with plugins like WP Migrate DB (Pro), or even manually, it's usually necessary to have the source and destination site running on the same table prefix.

Also, some people think that changing your table prefix from `wp_` to something else provides some security through obscurity. This probably isn't very true.

= What happens if something goes wrong =

It is imperative that you make and test backups of your database and wp-config.php file before running RDTP. If something goes wrong and you find that your site is broken, you will need to restore your site from those backups.

The first thing RDTP does is try to change the `$table_prefix` variable in your `wp-config.php` file, and this is the most likely point that you will encounter an error. If RDTP is unable to update your `wp-config.php` file, it won't continue on to update the database, so you'll probably just need to check the permissions on your `wp-config.php` and try again.

== Screenshots ==

1. Step 1 - confirm that you have backed up your database
2. Step 2 - confirm that you have backed up your wp-config.php
3. Step 3 - input your new table prefix or use the auto-generated one
4. Step 4 - db table prefix has been renamed successfully

== Changelog ==

= 0.1 =
* Initial Release

