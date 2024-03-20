<?php
/* 
Positive R. Test Locations Init Functions
Created: 01/21/2022
Last Update: 01/25/2022
Author: Gabriel Caroprese
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// if plugin Maps Marker Pro is not installed a message will show up
add_action( 'admin_notices', 'ik_prt_location_map_plugin_dependencies', 10);
function ik_prt_location_map_plugin_dependencies() {
    if (!class_exists('ARM_members') || !class_exists('MMP\Maps_Marker_Pro')) {
        $pluginURL = 'ik-armember-mapmarker-submiter/ik-armember-mapmarker-submiter.php';
        if (!class_exists('MMP\Maps_Marker_Pro')){
            echo '<div class="error"><p>' . __( 'Warning: The plugin "Positive R. Test Locations" needs <a href="https://www.mapsmarker.com/" target="_blank">Maps Marker Pro</a> in order to work.' ) . '</p></div>';
            deactivate_plugins($pluginURL);
        }
    }
}


//function to create tables in DB
function ik_prt_location_map_create_tables() {
    require_once('zipcodes.php');
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_entries = $wpdb->prefix . 'ik_prt_entries';
	$sql = "CREATE TABLE IF NOT EXISTS ".$table_entries." (
		id int(8) NOT NULL AUTO_INCREMENT,
	    marker_id int(8) NOT NULL,
		zip_code varchar(9) NOT NULL,
		zip_city varchar(18) NOT NULL,
		zip_state varchar(22) NOT NULL,
		num_pos_cases tinyint(20) NOT NULL,
		timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		phone varchar(22) DEFAULT '-' NOT NULL,
		email varchar(100) NOT NULL,
	    ip varchar(39) NOT NULL,
	    ip_city varchar(20) NOT NULL,
	    ip_state varchar(20) NOT NULL,
	    ip_country varchar(20) NOT NULL,
	    user_agent text NOT NULL,
		UNIQUE KEY id (id)
	) ".$charset_collate.";";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	//I add table with zipcodes
	ik_prt_location_map_add_zipcodes();
}

//I add style and scripts from plugin
function ik_prt_location_map_add_css_js() {
	wp_register_style( 'ik_prt_location_map_css', IK_PRT_LOCATION_MAP_PUBLIC . 'css/stylesheet.css', false, '1.1.5', 'all' );
	wp_enqueue_style('ik_prt_location_map_css');
}
add_action( 'admin_enqueue_scripts', 'ik_prt_location_map_add_css_js' );

//Add scripts for backend
add_action( 'wp_enqueue_scripts', 'ik_prt_location_map_add_frontend_js' );
function ik_prt_location_map_add_frontend_js() {
    wp_enqueue_script('ik_prt_location_map_form_script', IK_PRT_LOCATION_MAP_PUBLIC . '/js/geoloc_submit.js', array(), '2.1.16', true );
    wp_localize_script( 'ik_prt_location_map_form_script', 'ik_prt_location_ajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}
?>