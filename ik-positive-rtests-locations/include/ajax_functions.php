<?php
/*

Positive R. Test Locations - Ajax Functions
Created: 01/21/2022
Last Update: 03/12/2022
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

//Ajax function to send data from the form to DB
add_action('wp_ajax_nopriv_ik_prt_location_ajax_insert_form_data', 'ik_prt_location_ajax_insert_form_data');
add_action( 'wp_ajax_ik_prt_location_ajax_insert_form_data', 'ik_prt_location_ajax_insert_form_data');
function ik_prt_location_ajax_insert_form_data(){
    //default response
    $result = 'Error';
    if(isset($_POST['zipcode']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['num_positive'])){
        
        if (isset($_POST['admin_panel']) == 1){
            $admin_panel = true;
        } else {
            $admin_panel = false;
        }

        //If recaptcha is active
        if (ik_prt_location_map_get_recaptcha_form(true) && $admin_panel == false){
            if(isset($_POST['recaptcha']) || isset($_POST['g-recaptcha-response'])){
                if (isset($_POST['recaptcha'])){
                    $captcha = $_POST['recaptcha'];
                } else {
                    $captcha = $_POST['g-recaptcha-response'];
                }
            } else {
                $recapchaoptionData = get_option('ik_prt_location_recaptcha_option');
                if ($recapchaoptionData == 'Invisible'){
                    $captcha = true;
                } else {
                    $captcha = false;
                }
            }
            
            $secretKey = get_option('ik_prt_location_recaptcha_s');
            $ip = $_SERVER['REMOTE_ADDR'];
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
            $response = file_get_contents($url);
            $responseKeys = json_decode($response,true);
            
            if ($responseKeys["success"] === true){
                $recaptchaOk = true;
            } else{
                $recaptchaOk = false;
            }

        } else {
            $recaptchaOk = true;
        }
        
        if($recaptchaOk == true) {

            $num_positive = absint($_POST['num_positive']);
            
            if ($num_positive > 0 && ($num_positive < 7 || $admin_panel == true)){
                
                $city = sanitize_text_field($_POST['city']);
                $state = sanitize_text_field($_POST['state']);
                $zip_code = absint($_POST['zipcode']);
                        
                $phone = (isset($_POST['phone'])) ? sanitize_text_field($_POST['phone']) : '-';
                $email = (isset($_POST['email'])) ? sanitize_email($_POST['email']) : '';
                
                //I make sure zip code has 5 digits:
                $zip_code = str_pad($zip_code, 5, '0', STR_PAD_LEFT);
                
                //URL to get data using the https://geo.ipify.org service
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $api_url = 'https://geo.ipify.org/api/v2/country,city';
            
                //I get API key for geo IP
                $geoipapi = get_option('ik_prt_location_geoipapi');
            
                $url = "{$api_url}?apiKey={$geoipapi}&ipAddress={$ip_address}";
            
                $result = file_get_contents($url);
                    
                $ip_data = json_decode($result, true);
                //{ "ip": "8.8.8.8", "location": { "country": "US", "region": "California", "city": "Mountain View", "lat": 37.40599, "lng": -122.078514, "postalCode": "94043", "timezone": "-07:00", "geonameId": 5375481 }, "domains": [ "0d2.net", "003725.com", "0f6.b0094c.cn", "007515.com", "0guhi.jocose.cn" ], "as": { "asn": 15169, "name": "Google LLC", "route": "8.8.8.0/24", "domain": "https://about.google/intl/en/", "type": "Content" }, "isp": "Google LLC" }
                
                if (isset($ip_data['location'])){
        
                    $location_ip['city'] = $ip_data['location']['city'];
                    $location_ip['state'] = $ip_data['location']['region'];
                    $location_ip['country'] = $ip_data['location']['country'];
                    $location_ip['lat'] = $ip_data['location']['lat'];
                    $location_ip['lng'] = $ip_data['location']['lng'];
                } else {
                    $location_ip['city'] = 'API error';
                    $location_ip['state'] = 'API error';
                    $location_ip['country'] = 'API error';
                    $location_ip['lat'] = '0.000000';
                    $location_ip['lng'] = '0.000000';
                }
                
                
                //I get icon picture for map marker
                $icon_file = ik_prt_location_map_get_map_marker_icon();

                //I get the user agent data
                $user_agent = $_SERVER['HTTP_USER_AGENT'];

                //I get the customer ID and if it's O I assign the one from the admin
                $user_id = get_current_user_id();
                
                //If user is not registered /guest
                if ($user_id == 0){
                    $admins = get_users( array( 'role__in' => array( 'administrator' ) ) );
                    $user_id = $admins[0]->ID;
                }
        
                //I check has already records on the map
                $existing_records = ik_prt_location_map_city_exists($zip_code);
                
                if (!is_array($existing_records)){
                    $result = 'DB Error. Contact web Admin.';
                }
                
                if ($existing_records['result'] === false){
                    
                    //I set map marker name
                    $mmarkerName = $existing_records['city'].', '.$existing_records['state'];
                    
                    
                    //I import data to a new map marker record
                    global $wpdb;
                    $tableInsert = $wpdb->prefix.'mmp_markers';
                    $data_map = array (
                                    'name'=> $mmarkerName,	
                                    'lat'=> $existing_records['latitude'],
                                    'lng'=> $existing_records['longitude'],	
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
                    
                } else {

                    $mapmarker_id = $existing_records['marker_id'];
                    
                }
                    
                
                //I get map marker ID cases count
                $casescount = ik_prt_location_map_get_cases_count_by_id($mapmarker_id);
                
                //I add last cases
                $casescount = $casescount + $num_positive;
                
                $updateCountCases = ik_prt_location_map_update_cases($casescount, $mapmarker_id);
                
                
                //I insert data to the entries DB table
                global $wpdb;
                $tableRecords = $wpdb->prefix.'ik_prt_entries';
                $data_table_row = array (
                                'marker_id'=>$mapmarker_id,	
                                'zip_code'=>$zip_code,	
                                'zip_city'=>$city,	
                                'zip_state'=> $state,	
                                'num_pos_cases'=>$num_positive,	
                                'timestamp'=>current_time( 'mysql' ),	
                                'phone'=>$phone,	
                                'email'=>$email,	
                                'ip'=>$ip_address,	
                                'ip_city'=>$location_ip['city'],	
                                'ip_state'=>$location_ip['state'],	
                                'ip_country'=>$location_ip['country'],	
                                'user_agent'=>$user_agent,	
                        );
                $insertRecords = $wpdb->insert($tableRecords, $data_table_row);
                    
                update_option('datainserted', $data_table_row);    
                if ($insertRecords){
                    $result = 'Submitted!';
                } else {
                    $result = 'Error. Contact web Admin.';
                }
            } else {
                $result = 'Error. Wrong # of positive cases. Make sure is bigger than 0 and less than 7.';
            }
        } else {
                $result = "Error. Confirm you're not a Robot.";
        }   
    }
    
    echo json_encode( $result );
    wp_die();         
}

//Ajax to check if zip code is correct and get city and state
add_action('wp_ajax_nopriv_ik_prt_location_ajax_zip_check', 'ik_prt_location_ajax_zip_check');
add_action( 'wp_ajax_ik_prt_location_ajax_zip_check', 'ik_prt_location_ajax_zip_check');
function ik_prt_location_ajax_zip_check(){
    $zip_data['result'] = 'Wrong Zip code';
    if(isset($_POST['zipcode'])){
        if (strlen((string)$_POST['zipcode']) == 5){
            $zip_code = absint($_POST['zipcode']);
            
            //I make sure zip code has 5 digits after sanitizing
            $zip_code = str_pad($zip_code, 5, '0', STR_PAD_LEFT);
            
            //I get zip API data
            $zipemail = get_option('ik_prt_location_zipemail');
            $zipapi = get_option('ik_prt_location_zipapi');
            $zippassword = get_option('ik_prt_location_zippassword');
            
            
            //URL to get data using the zipapi.us service
            $url = 'https://service.zipapi.us/zipcode/'.$zip_code.'?X-API-KEY='.$zipapi.'&fields=geolocation,population';
            
            $curl = curl_init();
            
            // Connection
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERPWD, $zipemail . ":" . $zippassword);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            // Gettin data:
            $result = curl_exec($curl);
            if($result){
                //default answer from now
                $zip_data['result'] = 'Service Down';
                
                //I convert to object
                $location_data = json_decode($result, true);
                
                if (isset($location_data['data'])){
                    foreach ($location_data['data'] as $key=> $location){
                        $data_location[$key] = $location;
                    }
                    
                    if ($location_data['status'] == true){
    
                        $zip_data['city'] = $data_location['city'];
                        $zip_data['state'] = $data_location['state'];
                        $zip_data['result'] = 'OK';
                    }
                }
            }
        }
    }
    
    echo json_encode( $zip_data );
    wp_die();         
}


//Ajax to delete a record and map marker and reduce amount of cases per city
add_action( 'wp_ajax_ik_prt_location_ajax_delete_record', 'ik_prt_location_ajax_delete_record');
function ik_prt_location_ajax_delete_record(){
    if(isset($_POST['record_id'])){
        $record_id = absint($_POST['record_id']);
        
        global $wpdb;
        $table_delete = $wpdb->prefix.'ik_prt_entries';
        $action_delete = $wpdb->delete( $table_delete , array( 'id' => $record_id ) );
        
        //I recount cases
        $positive_cases = ik_prt_location_map_get_cases_count_by_id($mapmarker_id);
        
        if ($positive_cases == 0){
            $wpdb->query("DELETE FROM " . $wpdb->prefix . "mmp_markers WHERE id = ".$mapmarker_id);
        } else {
            $updateCountCases = ik_prt_location_map_update_cases($positive_cases, $mapmarker_id);
        }

        echo json_encode( true );
    }
    wp_die();         
}
?>