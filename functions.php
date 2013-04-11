<?php
ob_start();

// install & activate theme plugins
require_once ABSPATH . 'wp-admin/includes/plugin.php';
preg_match('/\/[^\/]+$/', TEMPLATEPATH, $theme_dir_name);
$theme_dir_name = $theme_dir_name[0];

$plugins = array("cloudzoom", "wp-showcase");
foreach ($plugins as $plugin){
	if(!file_exists(ABSPATH . "wp-content/plugins/$plugin")){
		echo symlink(
			ABSPATH . "wp-content/themes$theme_dir_name/plugins/$plugin",
			ABSPATH . "wp-content/plugins/$plugin"
		);
		activate_plugin("$plugin/$plugin.php");
	}
}


$curr_theme = wp_get_theme(TEMPLATEPATH . '/style.css');
$theme_version = trim( $curr_theme['Version'] );
if ( !$theme_version ) $theme_version = "1.0";

//Define constants:
define('S_FUNCTIONS', TEMPLATEPATH . '/functions/');
define('S_WIDGETS', TEMPLATEPATH . '/widgets/');
define('S_INCLUDES', TEMPLATEPATH . '/includes/');
define('S_THEME', 'Theme options');
define('S_THEME_DIR', get_bloginfo('template_directory'));
define('S_THEME_DOCS', S_THEME_DIR.'/functions/docs/docs.pdf');
define('S_THEME_LOGO', S_THEME_DIR.'/functions/img/logo.png');
define('S_MAINMENU_NAME', 'general-options');
define('S_THEME_VERSION', $theme_version);

//Load widgets
require_once S_WIDGETS . 'contact-form.php';
require_once S_WIDGETS . 'latest-portfolio.php';
require_once S_WIDGETS . 'latest-posts.php';
require_once S_WIDGETS . 'tabbed-widget.php';

//Load WP3 features:
require_once S_FUNCTIONS . 'register-wp3.php';

//Load all-purpose functions:
require_once S_FUNCTIONS . 'custom-functions.php';
require_once S_FUNCTIONS . 'comment-list.php';
require_once S_FUNCTIONS . 'shortcodes.php';

//Register widget areas:
require_once S_FUNCTIONS . 'register-widgets.php';

//Load admin specific files:
if ( is_admin() ) {
	require_once S_FUNCTIONS . 'admin-helper.php';
	require_once S_FUNCTIONS . 'ajax-image.php';
	require_once S_FUNCTIONS . 'generate-meta-box.php';
	require_once S_FUNCTIONS . 'generate-options.php';
	require_once S_FUNCTIONS . 'generate-slider.php';
	require_once S_FUNCTIONS . 'include-options.php';
	require_once S_FUNCTIONS . 'include-meta-boxes.php';
	require_once S_FUNCTIONS . 'meta-box-classes.php';
}

//Add admin styles/scripts:
add_action( 'admin_head', 's_admin_head' );


//Add scripts to header
if ( !is_admin() ) {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'superfish', S_THEME_DIR.'/assets/js/superfish.js' );
	wp_enqueue_script( 'jqueryui_custom', S_THEME_DIR.'/assets/js/jquery-ui-1.8.1.custom.min.js' );
	wp_enqueue_script( 's', S_THEME_DIR.'/assets/js/main.js' );
	wp_enqueue_script( 'comment-reply' );
}

//Add support for WP 3.0 features, thumbnails etc
add_theme_support( 'post-thumbnails' );
add_theme_support( 'menus' );
add_theme_support( 'automatic-feed-links' );

//Post thumbnail sizes
add_image_size( 's_thumb', 270, 170, true );

//Add translation support
load_theme_textdomain( 's', TEMPLATEPATH . '/language' );

//Register Widgets
add_action( 'widgets_init', 's_load_widgets' );
function s_load_widgets() {
	register_widget( 'S_Contact' );
	register_widget( 'S_Latest_Posts' );
	register_widget( 'S_Latest_Portfolio' );
	register_widget( 'S_Tabbed' );
}

//Remove shortcodes from excerpt and excerpt-RSS
function s_remove_shortcodes( $content ) {
	$content = strip_shortcodes( $content );
	return $content;
}
add_filter( 'the_excerpt_rss', 's_remove_shortcodes' );
add_filter( 'the_excerpt', 's_remove_shortcodes' );

//Change [...] to ... in the_excerpt()
function s_remove_ellipsis_brackets( $more ) {
	return '&hellip;';
}

add_filter( 'excerpt_more', 's_remove_ellipsis_brackets' );

//Add shortcode support to Widgets
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );
