<?php
/**
 * Plugin Name: Multisite logout all users
 * Description: Logout all user from all sites.
 * Version:     1.0.0
 * Author: m1tk00, campdoug
 *
 * @package mlu
 * @subpackage Wordpress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'plugins_loaded', 'mlu_plugins_loaded' );

/**
 * Add the logout functionality.
 */
function mlu_plugins_loaded() {
	if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
		add_action( 'clear_auth_cookie', 'mlu_forse_logout_on_sites', 99 );
	}
}

/**
 * Function that clears users sessions through all sites.
 */
function mlu_forse_logout_on_sites() {
	$sites = get_sites();
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		global $wpdb;
		$query = $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}usermeta` WHERE meta_key = 'session_tokens' and user_id = %d", get_current_user_id() );
		$wpdb->query( $query );
		wp_destroy_other_sessions();
		wp_destroy_all_sessions();
		restore_current_blog();
	}
}
