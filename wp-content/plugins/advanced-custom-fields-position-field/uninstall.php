<?php
/**
 * Uninstall procedure
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * Delete plugin tables
 * @return bool
 */
function acfpf_uninstall() {
	global $wpdb;

	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}countries`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}regions`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}cities`");

	return true;
}