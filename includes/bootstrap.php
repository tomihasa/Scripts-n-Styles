<?php
namespace unFocus\SnS;

require_once( "constants.php" );
require_once( "main.php" );
require_once( "class-sns-widget.php" );


if ( is_admin() && ! ( defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML ) ) {
	/*	NOTE: Setting the DISALLOW_UNFILTERED_HTML constant to
		true in the wp-config.php would effectively disable this
		plugin's admin because no user would have the capability.
	*/
	require_once( 'class-sns-admin.php' );
	Admin::init();

	require_once( 'class-sns-meta-box.php' );
	require_once( 'class-sns-code-editor.php' );
	require_once( 'class-sns-plugin-editor-page.php' );
	require_once( 'class-sns-theme-editor-page.php' );
	require_once( 'class-sns-settings-page.php' );
	require_once( 'class-sns-usage-page.php' );
	require_once( 'class-sns-global-page.php' );
	require_once( 'class-sns-hoops-page.php' );
	require_once( 'class-sns-theme-page.php' );
	require_once( 'class-sns-ajax.php' );
	require_once( 'class-sns-form.php' );
}