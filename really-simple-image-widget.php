<?php
/**
 * Plugin Name: Really Simple Image Widget
 * Plugin URI: http://nilambar.net/2014/06/really-simple-image-widget-wordpress.html
 * Description: Easiest way to add image in your sidebar
 * Author: Nilambar Sharma
 * Author URI: http://nilambar.net
 * Version: 1.4
 * Text Domain: really-simple-image-widget
 *
 * @package Really_Simple_Image_Widget
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define.
define( 'REALLY_SIMPLE_IMAGE_WIDGET_VERSION', '1.4' );
define( 'REALLY_SIMPLE_IMAGE_WIDGET_SLUG', 'really-simple-image-widget' );
define( 'REALLY_SIMPLE_IMAGE_WIDGET_BASENAME', basename( dirname( __FILE__ ) ) );
define( 'REALLY_SIMPLE_IMAGE_WIDGET_BASE_FILE', plugin_basename( __FILE__ ) );
define( 'REALLY_SIMPLE_IMAGE_WIDGET_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'REALLY_SIMPLE_IMAGE_WIDGET_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );

// Load Upload field.
require_once REALLY_SIMPLE_IMAGE_WIDGET_DIR . '/fields/upload/upload.php';

// Load widget.
require_once REALLY_SIMPLE_IMAGE_WIDGET_DIR . '/inc/widget.php';

/**
 * Register widget.
 */
function really_simple_image_widget_register() {

	register_widget( 'Really_Simple_Image_Widget' );

}
add_action( 'widgets_init', 'really_simple_image_widget_register' );

/**
 * Init.
 */
function really_simple_image_widget_init() {

	// Make plugin translation ready.
	load_plugin_textdomain( 'really-simple-image-widget' );

}

add_action( 'init', 'really_simple_image_widget_init' );
