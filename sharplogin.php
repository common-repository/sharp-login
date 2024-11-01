<?php
/**
 * @package		sharplogin
 * Plugin Name: Sharp Login
 * Plugin URI: https://sharpedge.io/
 * Description: customize login page, you can change background color, you can change logo on login screen.
 * You can change login URL to prevent Brute Force Attack. You can change login attempts, which can prevent Brute Force Attack. You can decide lockout time.
 * Version: 1.2.0
 * Author: Husnain Ahmed
 * Author URI: https://sharpedge.io/
 * License: GPL2
 * Text Domain: sharplogin
 */

defined( 'ABSPATH' ) || exit;

define( 'SHARPLOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SHARPLOGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHARPLOGIN_BASENAME', plugin_basename( __FILE__ ) );

include ('sharplogin_registeration.php');
include ('init.php');

register_activation_hook( __FILE__, function() {
    $sharplogin_settings = array();
    update_option( 'sharplogin_settings', $sharplogin_settings );
    update_option( 'sl_login_attempts_settings', array() );
    update_option( 'sharplogin_page', 'login' );
});

register_deactivation_hook( __FILE__, function() {
    delete_option( 'sharplogin_settings');
    delete_option( 'sl_login_attempts_settings');
    delete_option( 'sharplogin_page' );
});
new SharpPlugin();