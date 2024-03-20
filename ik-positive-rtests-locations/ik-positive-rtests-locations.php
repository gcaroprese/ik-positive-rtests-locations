<?php
/*
Plugin Name: Positive R. Test Locations
Description: Sends info from a form to a map marker. Use the shortcode [PRT_FORM] to show the form.
Version: 2.4.5
Author: Gabriel Caroprese
Requires at least: 5.3
Requires PHP: 7.2
*/ 

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$ik_prt_location_mapDir = dirname( __FILE__ );
$ik_prt_location_mapPublicDir = plugin_dir_url(__FILE__ );
define( 'IK_PRT_LOCATION_MAP_DIR', $ik_prt_location_mapDir);
define( 'IK_PRT_LOCATION_MAP_PUBLIC', $ik_prt_location_mapPublicDir);

require_once($ik_prt_location_mapDir . '/include/init.php');
require_once($ik_prt_location_mapDir . '/include/menus.php');
require_once($ik_prt_location_mapDir . '/include/functions.php');
require_once($ik_prt_location_mapDir . '/include/ajax_functions.php');
require_once($ik_prt_location_mapDir . '/include/form_shortcode.php');
register_activation_hook( __FILE__, 'ik_prt_location_map_create_tables' );

?>