<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Positive R. Test Locations Menus
Created: 01/21/2022
Last Update: 03/12/2022
Author: Gabriel Caroprese
*/

// I add menus on WP-admin
add_action('admin_menu', 'ik_prt_location_map_wpmenu', 999);
function ik_prt_location_map_wpmenu(){
    add_menu_page('PRT Locations', 'PRT Locations', 'manage_options', 'ik_prt_location_map', false, plugin_dir_url( __DIR__ ) . 'img/trp-plugin-icon.png' );
    add_submenu_page('ik_prt_location_map', 'Entries - PRT Locations', 'Entries', 'manage_options', 'ik_prt_location_map', 'ik_prt_location_map_entries_page', 2 );
    add_submenu_page('ik_prt_location_map', 'Add New - PRT Locations', 'Add New', 'manage_options', 'ik_prt_location_add_new_entry', 'ik_prt_location_add_new_entry', 2 );
    add_submenu_page('ik_prt_location_map', 'Config - PRT Locations', 'Config', 'manage_options', 'ik_prt_location_map_config_page', 'ik_prt_location_map_config_page', 3 );
}

//Function to add config panel content
function ik_prt_location_map_config_page(){
    include (IK_PRT_LOCATION_MAP_DIR.'/templates/config.php');
}

//Function to see entries panel content
function ik_prt_location_map_entries_page(){
    include (IK_PRT_LOCATION_MAP_DIR.'/templates/entries.php');
}

//Function to add entries panel content
function ik_prt_location_add_new_entry(){
    include (IK_PRT_LOCATION_MAP_DIR.'/templates/new_entries.php');
}

?>