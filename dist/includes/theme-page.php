<?php
namespace unFocus\SnS;

/**
 * Theme_Page
 *
 * Allows WordPress admin users the ability to edit theme CSS
 * and LESS directly in the admin via CodeMirror.
 *
 * On the `wp_enqueue_scripts` action, use `wp_enqueue_style( 'theme_style', get_stylesheet_uri() );` to add your style.css instead of inline.
 * On the `after_setup_theme` action, use `add_theme_support( 'scripts-n-styles', array( '/less/variables.less', '/less/mixins.less' ) );` but use the appropriate file locations.
 *
 */

add_action( 'admin_menu', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;
	if (  !current_theme_supports( 'scripts-n-styles' ) ) return;

	$hook_suffix = add_submenu_page(
		ADMIN_MENU_SLUG,
		__( 'Scripts n Styles', 'scripts-n-styles' ),
		__( 'Theme' ),
		'unfiltered_html',
		ADMIN_MENU_SLUG.'_theme',
		'\unFocus\SnS\page' );

	add_action( "load-$hook_suffix", '\unFocus\SnS\help' );
	add_action( "load-$hook_suffix", '\unFocus\SnS\take_action', 49 );
	add_action( "admin_print_styles-$hook_suffix", function() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';

		wp_enqueue_style( 'sns-options' );

		wp_enqueue_script(  'sns-theme-page' );
		wp_localize_script( 'sns-theme-page', '_SnS_options', array( 'theme' => $cm_theme ) );
	} );

	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	add_action( "load-$hook_suffix", function() {
		// added here to not effect other pages. Theme page requires JavaScript (less.js) or it doesn't make sense to save.
		add_filter( 'sns_show_submit_button', '__return_false' );

		register_setting(
			OPTION_GROUP,
			'SnS_options' );

		add_settings_section(
			'theme',
			__( 'Scripts n Styles Theme Files', 'scripts-n-styles' ),
			function() {
				$files = array();
				$support_files = get_theme_support( 'scripts-n-styles' );

				if ( is_child_theme() )
					$root = get_stylesheet_directory();
				else
					$root = get_template_directory();

				foreach( $support_files[0] as $file ) {
					if ( is_file( $root . $file ) )
						$files[] = $root . $file;
				}

				$slug = get_stylesheet();
				$options = get_option( 'SnS_options' );
				// Stores data on a theme by theme basis.
				$theme =  isset( $options[ 'themes' ][ $slug ] ) ? $options[ 'themes' ][ $slug ] : array();
				$stored =  isset( $theme[ 'less' ] ) ? $theme[ 'less' ] : array(); // is an array of stored imported less file data
				$compiled = isset( $theme[ 'compiled' ] ) ? $theme[ 'compiled' ] : ''; // the complete compiled down css
				$slug = esc_attr( $slug );

				$open_theme_panels = json_decode( get_user_option( 'sns_open_theme_panels', get_current_user_id() ), true );

				?>
				<div style="overflow: hidden">
				<div id="less_area" style="width: 49%; float: left; overflow: hidden; margin-right: 2%;">
				<?php
				foreach ( $files as $file ) {
					$name = basename( $file );
					$raw = file_get_contents( $file );
					if ( isset( $stored[ $name ] ) ) {
						$source = $stored[ $name ];
						$less = isset( $source ) ? $source : '';
						$compiled = isset( $compiled ) ? $compiled : '';
					} else {
						$less = $raw;
						$compiled = '';
					}
					$name = esc_attr( $name );
					$lead_break = 0 == strpos( $less, PHP_EOL ) ? PHP_EOL : '';
					if ( isset( $open_theme_panels[ $name ] ) )
						$collapse = $open_theme_panels[ $name ] == 'yes' ? 'sns-collapsed ' : '';
					else
						$collapse = $less == $raw ? 'sns-collapsed ': '';
					?>
					<div class="sns-less-ide" style="overflow: hidden">
					<div class="widget"><div class="<?php echo $collapse; ?>inside">
						<span class="sns-collapsed-btn"></span>
						<label style="margin-bottom: 0;"><?php echo $name ?></label>
						<textarea data-file-name="<?php echo $name ?>" data-raw="<?php echo esc_attr( $raw ) ?>"
							name="SnS_options[themes][<?php echo $slug ?>][less][<?php echo $name ?>]"
							style="min-width: 250px; width:47%;"
							class="code less" rows="5" cols="40"><?php echo $lead_break . esc_textarea( $less ) ?></textarea>
						<div class="sns-ajax-wrap">
							<a class="sns-ajax-load button" href="#">Load Source File</a>
							<a class="sns-ajax-save button" href="#">Save All Changes</a>
							<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>
							<div class="single-status"><div class="updated settings-error below-h2"></div></div>
						</div>
					</div></div>
					</div>
					<?php
				}
				?>
				</div>
				<div id="css_area" class="sns-less-ide" style="width: 49%; float: left; overflow: hidden;">
					<div id="compile_status" style="display: none" class="updated settings-error below-h2">
						<p><span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>
						<span class="status-text">Keystokes detected. 1 second delay, then compiling...</span></p>
					</div>
					<div class="widget"><div class="sns-collapsed inside">
						<span class="sns-collapsed-btn"></span>
						<label style="margin-bottom: 0;">Preview Window</label>
						<textarea
							name="SnS_options[themes][<?php echo $slug ?>][compiled]"
							style="min-width: 250px; width:47%;"
							class="code css" rows="5" cols="40"><?php echo esc_textarea( $compiled ) ?></textarea>
					</div></div>
					<div id="compiled_error" class="error settings-error below-h2"></div>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG );
	} );
} );