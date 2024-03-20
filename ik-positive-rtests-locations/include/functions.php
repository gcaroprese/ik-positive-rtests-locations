<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Positive R. Test Locations Functions 
Created: 01/21/2022
Last Update: 03/12/2022
Author: Gabriel Caroprese
*/


//Function to list maps ID to assign map markers
function ik_prt_location_map_id_option_list(){
    $defaultOption = '<option value="0">Select Map ID</option>';
    
    $options = $defaultOption;
    
    global $wpdb;
    $prt_locationmaps = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "mmp_maps ORDER BY id DESC");
    
    if (isset($prt_locationmaps[0]->id)){
        foreach ($prt_locationmaps as $prt_locationmap){
            $options .= '<option value="'.$prt_locationmap->id.'">'.$prt_locationmap->name.' - #'.$prt_locationmap->id.'</option>';
        }
    }
    
    return $options;

}


//Option list of icons from map marker uploads folder for select 
function ik_prt_location_map_icons_list(){
    $upload_dir = wp_upload_dir();
    $mapmarkersdir = $upload_dir['basedir'].'/maps-marker-pro/icons';
    
    $icon_files = list_files($mapmarkersdir);
    
    if (is_array($icon_files)){
        sort($icon_files);
        $icons_select = '';
        foreach($icon_files as $icon_file){
            $url_icon_array = explode('/', $icon_file);
            $icon_file = end($url_icon_array);
            $icon_src = $upload_dir['baseurl'].'/maps-marker-pro/icons/'.$icon_file;
            $icons_select .= '<option value="'.$icon_file.'" style="background-image:url('.$icon_src.');">'.$icon_file.'</option>';
        }
    }
    
    return $icons_select;

}


//I check if there are records on a map marker about a city
function ik_prt_location_map_city_exists($zip_code){
    
    $zip_code = absint($zip_code);
    
    //I search on the zip table to get the name, lat and long of the city
    global $wpdb;
    $prt_zipcode = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ik_prt_zipcodes WHERE zip_code LIKE '".$zip_code."'");
    
    if (isset($prt_zipcode[0]->id)){
        $zipdata['city'] = $prt_zipcode[0]->zip_city;
        $zipdata['state'] = $prt_zipcode[0]->state_short;
        $zipdata['latitude'] = $prt_zipcode[0]->latitude;
        $zipdata['longitude'] = $prt_zipcode[0]->longitude;

        //I search entries from the same city and state
        global $wpdb;
        $prt_entry = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ik_prt_entries WHERE zip_city LIKE '".$zipdata['city']."' AND zip_state LIKE '".$zipdata['state']."'");
        
        //If I found an entry with the same city name and state
        if (isset($prt_entry[0]->id)){
            if (absint($prt_entry[0]->marker_id) > 0){
                $zipdata['marker_id'] = absint($prt_entry[0]->marker_id);

                //I make sure marker ID exists
                global $wpdb;
                $prt_mapmarker = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "mmp_markers WHERE id = ".$zipdata['marker_id']);
                
                //If I don't find the map marker id assigned
                if (!isset($prt_mapmarker[0]->id)){
                    //I rebuild entry on map markers table
                    ik_prt_location_map_rebuild_entries();
                } 

                $zipdata['result'] = true;
                
            }
        } else {
            $zipdata['result'] = false;
        }
        
        return $zipdata;
    }

    return false;
}


//function to rebuild entries on the map markers
function ik_prt_location_map_rebuild_entries(){
    
    global $wpdb;
    $queryentries = "SELECT * FROM " . $wpdb->prefix . "ik_prt_entries ORDER BY marker_id DESC";
    $entries = $wpdb->get_results($queryentries);
    
    if (isset($entries[0]->id)){
        
        //I set base to start counting cases based on the actual city and state
        $old_marker_id = $entries[0]->marker_id;
        $old_city = $entries[0]->zip_city;
        $old_state = $entries[0]->zip_state;
        $existchecked = false;
        $total_records = count($entries);
        $count_records = 0;
        
        foreach ($entries as $record){
            $count_records = $count_records + 1;
            $marker_id = $record->marker_id;
            $city = $record->zip_city;
            $state = $record->zip_state;
            $zip_code = $record->zip_code;
            
            if ($city != $old_city && $old_state != $state){
                ik_prt_location_map_reassign_record($record);
            } else {
                
               /**
                * I update cases of last map marker processed
                */
            
                //I get map marker ID cases count
                $casescount = ik_prt_location_map_get_cases_count_by_id($old_marker_id);
                
                $updateCountCases = ik_prt_location_map_update_cases($casescount, $old_marker_id);
                
                //I restart the existchecked value
                $existchecked = false;
            }
            
            //I reassign values
            $old_marker_id = $record->marker_id;
            $old_city = $record->zip_city;
            $old_state = $record->zip_state;
            
            //If map marker ID wasn't checked before
            if ($existchecked == false){
                
                //I mark it as already checked
                $existchecked = true;
                
                $existing_records = ik_prt_location_map_city_exists($zip_code);
    
                if (is_array($existing_records)){
                    if ($existing_records['result'] === false){
                        ik_prt_location_map_reassign_record($record); 
                    }
                }
                
            }
            
            
            //In case is the last record I update cases of the processed entry
            if ($count_records == $total_records){
               /**
                * I update cases of last map marker processed
                */
            
                //I get map marker ID cases count
                $casescount = ik_prt_location_map_get_cases_count_by_id($marker_id);
                
                $updateCountCases = ik_prt_location_map_update_cases($casescount, $marker_id);
            
            }
            
        }
        
    }
    return;
}

//function to reassign entries to a different map marker
function ik_prt_location_map_reassign_record($entry_data){
    
    $mapmarker_id = absint($entry_data->marker_id);
    $city = sanitize_text_field($entry_data->zip_city);
    $state = sanitize_text_field($entry_data->zip_state);
    
    
    //I search for an entry with the same city but different marker
    global $wpdb;
    $queryentries = "SELECT * FROM " . $wpdb->prefix . "ik_prt_entries WHERE marker_id != ".$mapmarker_id." AND zip_city LIKE '".$city."' AND zip_state LIKE '".$state."' ORDER BY marker_id DESC";
    $entries = $wpdb->get_results($queryentries);
    
    if (isset($entries[0]->marker_id)){
        
        //Reasign all entries with the that marker_id, city and state to this different marker ID
        global $wpdb;
        $wpdb->query("UPDATE " . $wpdb->prefix . "ik_prt_entries SET marker_id='$entries[0]->marker_id' WHERE marker_id = ".$mapmarker_id." AND zip_city LIKE '".$city."' AND zip_state LIKE '".$state."'");
        
        
        //I make sure map marker exists
        global $wpdb;
        $prt_mapmarker = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "mmp_markers WHERE id = ".$mapmarker_id);
        
        //If I don't find the map marker id assigned
        if (isset($prt_mapmarker[0]->id)){
            $createMapMarker = false;
        } else {
            $createMapMarker = true;
        }
     
    } else {
        //I have to create a map marker
        $createMapMarker = true;
    }

    //If I have to create a map marker
    if ($createMapMarker === true){
        //I search on the zip table to get the name, lat and long of the city
        global $wpdb;
        $prt_zipcode = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ik_prt_zipcodes WHERE zip_code LIKE '".$zip_code."'");
        
        if (isset($prt_zipcode[0]->id)){
            $zipdata['city'] = $prt_zipcode[0]->zip_city;
            $zipdata['state'] = $prt_zipcode[0]->state_short;
            $zipdata['latitude'] = $prt_zipcode[0]->latitude;
            $zipdata['longitude'] = $prt_zipcode[0]->longitude;        
        

            //I set map marker name
            $mmarkerName = $zipdata['city'].', '.$zipdata['state'];
            
            //I get icon picture for map marker
            $icon_file = ik_prt_location_map_get_map_marker_icon();

            //I get the user agent data
            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            //I get the customer ID and if it's O I assign the one from the admin
            $user_id = get_current_user_id();
            
            
            //I import data to a new map marker record
            global $wpdb;
            $tableInsert = $wpdb->prefix.'mmp_markers';
            $data_map = array (
                            'name'=> $mmarkerName,	
                            'lat'=> $zipdata['latitude'],
                            'lng'=> $zipdata['longitude'],	
                            'zoom'=>'14.0',	
                            'icon'=> $icon_file,	
                            'blank'=> 0,	
                            'created_by_id'=>$user_id,	
                            'created_on'=>current_time( 'mysql' ),	
                            'updated_by_id'=>$user_id,
                            'updated_on'=>current_time( 'mysql' ),	
                    );
            $rowResult = $wpdb->insert($tableInsert, $data_map);
            $mapmarker_id = $wpdb->insert_id;
            
            //If there's a map assigned I assign the map marker to a map ID
            $map_id_to_assign = absint(get_option('ik_prt_location_map_map_id'));
    
            //If there's a map assigned        
            if ($map_id_to_assign !== 0 ){
        
                //I assigned the map marker to the map ID
                global $wpdb;
                $tableInsertr = $wpdb->prefix.'mmp_relationships';
                $data_map_relationship = array (
                                'map_id'=> $map_id_to_assign,	
                                'type_id'=> '2',	
                                'object_id'=> $mapmarker_id,	
                        );
                $rowResult = $wpdb->insert($tableInsertr, $data_map_relationship);
        
            }
            
        }
        
        //I update marker ID with map marker created
        global $wpdb;
        $wpdb->query("UPDATE " . $wpdb->prefix . "ik_prt_entries SET marker_id='$mapmarker_id' WHERE marker_id = ".$entries[0]->marker_id);
         
         
        //I get map marker ID cases count
        $casescount = ik_prt_location_map_get_cases_count_by_id($mapmarker_id);
        
        $updateCountCases = ik_prt_location_map_update_cases($casescount, $mapmarker_id);
        
    }
    
    return;
}

//function to update positive cases at map marker
function ik_prt_location_map_update_cases($casescount, $mapmarker_id){
    
    $casescount = absint($casescount);
    $mapmarker_id = absint($mapmarker_id);
    
    $data_cases_html = '<p style="text-align: center;">'.$casescount.' cases</p>';                    
                        
    //I add/update the popup content with the number of cases
    global $wpdb;
    $tableupdate = $wpdb->prefix.'mmp_markers';
    $where = [ 'id' => $mapmarker_id ];
        
    $data_cases  = array (
                    'popup'=>$data_cases_html,
            );
    $rowResult = $wpdb->update($tableupdate,  $data_cases , $where);  
    
    return true;
    
}


//Count cases based on mapmarkerID of entries
function ik_prt_location_map_get_cases_count_by_id($mapmarker_id){
    
    $mapmarker_id = absint($mapmarker_id);
    
    //I search for an entry with the marker_id
    global $wpdb;
    $queryentries = "SELECT * FROM " . $wpdb->prefix . "ik_prt_entries WHERE marker_id = ".$mapmarker_id;
    $entries = $wpdb->get_results($queryentries);
    
    if (isset($entries[0]->marker_id)){
        $casesCount = 0;
        foreach ($entries as $record){
            $cases = absint($record->num_pos_cases);
            $casesCount = $casesCount + $cases;
        }
        
        return $casesCount;
    
    } else {
        return 0;
    }
}

//function to get map marker icon
function ik_prt_location_map_get_map_marker_icon(){
    $icon_file = get_option('ik_prt_location_map_map_icon');
    if ($icon_file == false || $icon_file == NULL ){
        //If no icon assigned I select first from the list
        $upload_dir = wp_upload_dir();
        $mapmarkersdir = $upload_dir['basedir'].'/maps-marker-pro/icons';
        
        $icon_files = list_files($mapmarkersdir);
        
        if (is_array($icon_files)){
            sort($icon_files);
            $url_icon_array = explode('/', $icon_files[0]);
            $icon_file = end($url_icon_array);
        }
    }
    
    return $icon_file;

}

//Check if recaptcha is enabled
function ik_prt_location_map_get_recaptcha_form($ifactive = false){
        
    $recapchacheckUse = get_option('ik_prt_location_recaptcha_use');
    
    if ($recapchacheckUse != false && $recapchacheckUse != '0' && $recapchacheckUse != NULL){
        $recapchaEnabled = true;
    } else {
        $recapchaEnabled = false;
    }
    
    if ($recapchaEnabled == true){
        $recaptchakey = get_option('ik_prt_location_recaptcha_k');
        $recaptchasecret = get_option('ik_prt_location_recaptcha_s');
        
        if ($recaptchakey == false || $recaptchakey == NULL || $recaptchasecret == false || $recaptchasecret == NULL){
            //No keys
            return;
        } 
        
        //I check if it's "I'm not a robot" or "invisible" recaptcha
        $recapchaoptionData = get_option('ik_prt_location_recaptcha_option');
        if ($recapchaoptionData == 'Invisible'){
            $recaptcha = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="g-recaptcha"
                  data-sitekey="'.$recaptchakey.'"
                  data-callback="recaptchaOnSubmit"
                  data-size="invisible">
            </div>
            <input type="hidden" name="recaptcha_data_confirm" id="recaptcha_data_confirm" value="">
            <script>
                function recaptchaOnSubmit() {
                    jQuery("#recaptcha_data_confirm").val("done");
                }
            </script>';
            
        } else {
        
            $recaptcha = "<script src='https://www.google.com/recaptcha/api.js' async defer></script>
            <p>
                <div class='g-recaptcha' data='robot' data-sitekey='".$recaptchakey."'></div>
            </p>";
        }
        
        //If I only want to know if is active
        if ($ifactive == true){
            return $recapchaEnabled;
        } else {
            return $recaptcha;
        }

        
    } else{
        //recaptcha disabled
        return;
    }
}


?>