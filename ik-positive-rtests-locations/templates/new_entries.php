<?php
/* 
Positive R. Test Locations - New Entries Page
Created: 01/21/2022
Last Update: 01/24/2022
Author: Gabriel Caroprese
*/

if ( ! defined('ABSPATH')) exit('restricted access');

wp_enqueue_script('ik_prt_location_map_form_script', IK_PRT_LOCATION_MAP_PUBLIC . '/js/geoloc_submit_admin.js', array(), '2.1.11', true );
wp_localize_script( 'ik_prt_location_map_form_script', 'ik_prt_location_ajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

?>
<style>
    #ik_prt_location_new_records label, #ik_prt_location_new_records input{
        display: block;
    }
    #ik_prt_location_new_records label, #ik_prt_submit_form{
        margin-top: 10px;
    }
    #ik_prt_location_new_records .data-field-name{
        padding-left: 4px;
    }
</style>
<div id ="ik_prt_location_new_records">
    <h1>Positive R. Test Locations - Add New Records</h1>
    <div name="ik_prt_form"  id="ik_prt_form">
        <input type="hidden" id="ik_prt_phone" name="ik_prt_phone">
        <input type="hidden" id="ik_prt_email" name="ik_prt_email">
        <label>
            <span class="data-field-name">U.S. Zip Code <span style="color: red"> *</span></span>
            <input required type="text" id="ik_prt_zip_code" name="ik_prt_zip_code" pattern="[0-9]{5}" min="5" max="5" maxlength="5" placeholder="ex. 10022">
        </label>
        <label>
            <span class="data-field-name">City <span style="color: red"> *</span><span>
            <input required type="text" disabled id="ik_prt_city" name="ik_prt_city" value="">
        </label>
        <label>
            <span class="data-field-name">State <span style="color: red"> *</span></span>
            <input required type="text" disabled id="ik_prt_state" name="ik_prt_state" value="">
        </label>
        <label>
            <span class="data-field-name"># of People Positive <span style="color: red"> *</span></span>
            <input required type="text" id="ik_prt_positive_cases" name="ik_prt_positive_cases" min="1">
        </label>
        <button type="submit" id="ik_prt_submit_form" class="button-primary button">SUBMIT</button>
        <div id="ik_prt_message_form"></div>
    </div>

</div>