<?php
namespace unFocus\SnS;

/**
 * Scripts n Styles Admin
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

/**
 * Adds link to the Settings Page in the WordPress "Plugin Action Links" array.
 * @param array $actions
 * @return array
 */
add_filter( 'plugin_action_links_'.BASENAME, function( $actions ) {
	$actions[ 'settings' ] = '<a href="' . menu_page_url( ADMIN_MENU_SLUG.'_settings', false ) . '"/>' . __( 'Settings' ) . '</a>';
	return $actions;
} );