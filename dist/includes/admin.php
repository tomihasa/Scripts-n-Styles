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
 * Adds CodeMirror Theme to WordPress's instances.
 */	
add_action( 'admin_enqueue_scripts', function() { 
	$options = get_option( 'SnS_options' );
	
	if ( ! empty( $options[ 'cm_theme' ] ) )
		wp_enqueue_style('sns-codemirror'); 
} );
add_filter( 'wp_code_editor_settings', function( $settings, $args ) {
	$options = get_option( 'SnS_options' );
	
	if ( ! empty( $options[ 'cm_theme' ] ) ) 
		$settings['codemirror']['theme'] = $options[ 'cm_theme' ];
	
	return $settings;
}, 10, 2 );


/**
 * Adds link to the Settings Page in the WordPress "Plugin Action Links" array.
 * @param array $actions
 * @return array
 */
add_filter( 'plugin_action_links_'.BASENAME, function( $actions ) {
	$actions[ 'settings' ] = '<a href="' . menu_page_url( ADMIN_MENU_SLUG.'_settings', false ) . '"/>' . __( 'Settings' ) . '</a>';
	return $actions;
} );


// Add menu to admin bar
add_action( 'wp_before_admin_bar_render', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;
	global $wp_admin_bar;
	$title = WP_DEBUG ? 'Scripts n Styles (PHP: '.PHP_VERSION.')': 'Scripts n Styles';
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => __($title,'scripts-n-styles'),
		'href'  => admin_url( 'admin.php?page='.ADMIN_MENU_SLUG ),
		'meta'  => array( 'class' => 'Scripts_n_Styles' )
	] );
}, 11 );

function get_registered_scripts() {
	return [
	'utils', 'common', 'sack', 'quicktags', 'colorpicker', 'editor', 'wp-fullscreen', 'wp-ajax-response', 'wp-pointer', 'autosave',
	'heartbeat', 'wp-auth-check', 'wp-lists', 'prototype', 'scriptaculous-root', 'scriptaculous-builder', 'scriptaculous-dragdrop',
	'scriptaculous-effects', 'scriptaculous-slider', 'scriptaculous-sound', 'scriptaculous-controls', 'scriptaculous', 'cropper',
	'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-bounce',
	'jquery-effects-clip', 'jquery-effects-drop', 'jquery-effects-explode', 'jquery-effects-fade', 'jquery-effects-fold',
	'jquery-effects-highlight', 'jquery-effects-pulsate', 'jquery-effects-scale', 'jquery-effects-shake', 'jquery-effects-slide',
	'jquery-effects-transfer', 'jquery-ui-accordion', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker',
	'jquery-ui-dialog', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse', 'jquery-ui-position',
	'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable', 'jquery-ui-slider', 'jquery-ui-sortable',
	'jquery-ui-spinner', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-ui-widget', 'jquery-form', 'jquery-color', 'suggest',
	'schedule', 'jquery-query', 'jquery-serialize-object', 'jquery-hotkeys', 'jquery-table-hotkeys', 'jquery-touch-punch',
	'jquery-masonry', 'thickbox', 'jcrop', 'swfobject', 'plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight',
	'plupload-html4', 'plupload-all', 'plupload-handlers', 'wp-plupload', 'swfupload', 'swfupload-swfobject', 'swfupload-queue',
	'swfupload-speed', 'swfupload-all', 'swfupload-handlers', 'comment-reply', 'json2', 'underscore', 'backbone', 'wp-util',
	'wp-backbone', 'revisions', 'imgareaselect', 'mediaelement', 'wp-mediaelement', 'password-strength-meter', 'user-profile',
	'user-suggest', 'admin-bar', 'wplink', 'wpdialogs', 'wpdialogs-popup', 'word-count', 'media-upload', 'hoverIntent', 'customize-base',
	'customize-loader', 'customize-preview', 'customize-controls', 'accordion', 'shortcode', 'media-models', 'media-views',
	'media-editor', 'mce-view', 'less.js', 'coffeescript', 'chosen', 'coffeelint', 'mustache', 'html5shiv', 'html5shiv-printshiv',
	'google-diff-match-patch', 'codemirror' ];
}


/**
 * Settings Page help
 */
function help() {
	$help  = '<p>' . __( 'In default (non MultiSite) WordPress installs, both <em>Administrators</em> and <em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only <em>"Super Admin"</em> users can access either <em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change capabilities (specifically "unfiltered_html"), other users can be granted access.', 'scripts-n-styles' ) . '</p>';
	$help .= '<p><strong>' . __( 'Reference: jQuery Wrappers', 'scripts-n-styles' ) . '</strong></p>'
		  . '<pre><code>jQuery(document).ready(function($) {' . PHP_EOL
		  . '	// $() will work as an alias for jQuery() inside of this function' . PHP_EOL
		  . '});</code></pre>';
	$help .= '<pre><code>(function($) {' . PHP_EOL
		  . '	// $() will work as an alias for jQuery() inside of this function' . PHP_EOL
		  . '})(jQuery);</code></pre>';
	$sidebar = '<p><strong>' . __( 'For more information:', 'scripts-n-styles' ) . '</strong></p>' .
				'<p>' . __( '<a href="http://wordpress.org/extend/plugins/scripts-n-styles/faq/" target="_blank">Frequently Asked Questions</a>', 'scripts-n-styles' ) . '</p>' .
				'<p>' . __( '<a href="https://github.com/unFocus/Scripts-n-Styles" target="_blank">Source on github</a>', 'scripts-n-styles' ) . '</p>' .
				'<p>' . __( '<a href="http://wordpress.org/tags/scripts-n-styles" target="_blank">Support Forums</a>', 'scripts-n-styles' ) . '</p>';
	$screen = get_current_screen();
	if ( method_exists( $screen, 'add_help_tab' ) ) {
		$screen->add_help_tab( array(
			'title' => __( 'Scripts n Styles', 'scripts-n-styles' ),
			'id' => 'scripts-n-styles',
			'content' => $help
			)
		);
		if ( 'post' != $screen->id )
			$screen->set_help_sidebar( $sidebar );
	} else {
		add_contextual_help( $screen, $help . $sidebar );
	}
}