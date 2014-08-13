<?php
/*
Plugin Name: Canvas Extension - BBPress
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that make BBPress works with Canvas
Version: 1.0
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-bbpress-functions.php' );
require_once( 'classes/class-pootlepress-bbpress.php' );
require_once( 'classes/class-pootlepress-updater.php');

$GLOBALS['pootlepress_bbpress'] = new Pootlepress_BBPress( __FILE__ );
$GLOBALS['pootlepress_bbpress']->version = '1.0';

add_action('init', 'pp_bbp_updater');
function pp_bbp_updater()
{
    if (!function_exists('get_plugin_data')) {
        include(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $data = get_plugin_data(__FILE__);
    $wptuts_plugin_current_version = $data['Version'];
    $wptuts_plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
    $wptuts_plugin_slug = plugin_basename(__FILE__);
    new Pootlepress_Updater ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}
?>
