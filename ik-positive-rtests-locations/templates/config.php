<?php

/* 
Positive R. Test Locations - Config Page
Created: 01/21/2022
Last Update: 01/23/2022
Author: Gabriel Caroprese
*/

if ( ! defined('ABSPATH')) exit('restricted access');
?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if (isset($_POST['ik_prt_location_map_map_id'])){
        $map_id_submited = absint($_POST['ik_prt_location_map_map_id']);
        update_option('ik_prt_location_map_map_id', $map_id_submited);
    }
    if (isset($_POST['ik_prt_location_map_map_icon'])){
        $map_icon_submited = sanitize_text_field($_POST['ik_prt_location_map_map_icon']);
        update_option('ik_prt_location_map_map_icon', $map_icon_submited);
    }

    if (isset($_POST['recapkey']) && isset($_POST['recapseckey'])){
        $ik_prt_location_recaptcha_k = sanitize_text_field($_POST['recapkey']);
        $ik_prt_location_recaptcha_s = sanitize_text_field($_POST['recapseckey']);
    
        if (isset($_POST['userecaptcha'])){
            $checkbox_recaptcha = "1";
        } else {
            $checkbox_recaptcha = "0";
        }
        
        $recaptcha_option = "Robot";
        if (isset($_POST['userecaptcha_option'])){
            if($_POST['userecaptcha_option'] == 'Invisible'){
                $recaptcha_option = "Invisible";
            }
        }
        
        update_option('ik_prt_location_recaptcha_k', $ik_prt_location_recaptcha_k);
        update_option('ik_prt_location_recaptcha_s', $ik_prt_location_recaptcha_s);
        update_option('ik_prt_location_recaptcha_use', $checkbox_recaptcha);        
        update_option('ik_prt_location_recaptcha_option', $recaptcha_option);        
    }
    
    if (isset($_POST['geoipapi'])){
        $geoipapi = sanitize_text_field($_POST['geoipapi']);
        
        update_option('ik_prt_location_geoipapi', $geoipapi);
    }
    
    if (isset($_POST['zipapi']) && isset($_POST['zipemail']) && isset($_POST['zippassword'])){
        $zipemail = sanitize_email($_POST['zipemail']);
        $zipapi = sanitize_text_field($_POST['zipapi']);
        $zippassword = sanitize_text_field($_POST['zippassword']);
    
        update_option('ik_prt_location_zipemail', $zipemail);
        update_option('ik_prt_location_zipapi', $zipapi);
        update_option('ik_prt_location_zippassword', $zippassword);        
    }

    if (isset($_POST['emgdaddy_form_id'])){
        $emgdaddy_s = sanitize_text_field($_POST['emgdaddy_form_id']);
        
        update_option('ik_prt_location_emgdaddy_form_id', $emgdaddy_s);
    }

}
  
// Check the value of the form saved
$map_id_saved = get_option('ik_prt_location_map_map_id');
if ($map_id_saved == false || $map_id_saved == NULL ){
    $map_id_saved = 0;
}
$map_icon_saved = get_option('ik_prt_location_map_map_icon');
if ($map_icon_saved == false || $map_icon_saved == NULL ){
    $map_icon_saved = 0;
}


$zipemail = get_option('ik_prt_location_zipemail');
$zipapi = get_option('ik_prt_location_zipapi');
$zippassword = get_option('ik_prt_location_zippassword');  
$geoipapi = get_option('ik_prt_location_geoipapi');
$recaptchakey = get_option('ik_prt_location_recaptcha_k');
$recaptchasecret = get_option('ik_prt_location_recaptcha_s');
$recapchacheckData = get_option('ik_prt_location_recaptcha_use');
$recapchaoptionData = get_option('ik_prt_location_recaptcha_option');
$emgdaddy_form_id = get_option('ik_prt_location_emgdaddy_form_id');



if ($zipemail == false || $zipemail == NULL){
    $zipemail = '';
}
if ($zipapi == false || $zipapi == NULL){
    $zipapi = '';
}
if ($zippassword == false || $zippassword == NULL){
    $zippassword = '';
}
if ($geoipapi == false || $geoipapi == NULL){
    $geoipapi = '';
}
if ($recaptchakey == false || $recaptchakey == NULL){
    $recaptchakey = '';
}
if ($recaptchasecret == false || $recaptchasecret == NULL){
    $recaptchasecret = '';
}
if ($recapchacheckData != false && $recapchacheckData != '0' && $recapchacheckData != NULL){
    $recapchacheck = 'checked';
} else {
    $recapchacheck = '';
}
if ($emgdaddy_form_id == false || $emgdaddy_form_id == NULL){
    $emgdaddy_form_id = '';
}

$map_ids_option_list = ik_prt_location_map_id_option_list();
$map_icons = ik_prt_location_map_icons_list();



$robotchecked = 'checked';
$invisiblechecked = '';
if ($recapchaoptionData == 'Invisible'){
    $robotchecked = '';
    $invisiblechecked = 'checked';
}

?>

<style>
.error, .updated, #setting-error-tgmpa{display: none! important;}
</style>
<div id="ik_prt_location_map_config">
    <h1>Positive R. Test Locations - Config</h1>
    <form action="" method="post" id="ik_prt_location_map_config_form" enctype="multipart/form-data" autocomplete="no">
        <p>
             <label>
    			<span>Select Map ID to be assigned</span><br />
    			<select required id="ik_prt_location_map_map_id" name="ik_prt_location_map_map_id">
    			    <option value="0">Leave unassigned</option>
    			    <?php echo $map_ids_option_list; ?>
    			</select>
            </label>
        </p>
        <p>
             <label>
    			<span>Select Map Marker Icon to be assigned</span><br />
    			<select required id="ik_prt_location_map_map_icon" name="ik_prt_location_map_map_icon">
    			    <?php echo $map_icons; ?>
    			</select>
            </label>
        </p>
        <hr>
        <h3>Geo Location by IP</h3>
        <p>Sign up at <a href="geo.ipify.org" target="_blank">Geo.ipify.org</a></p>
        <p>
            <label for="geoip-api">
                <span>Geo IP API</span><br />
                <input type="text" name="geoipapi" value="<?php echo $geoipapi; ?>" />
            </label>
        </p>
        <hr>
        <h3>ZIP Code API</h3>
        <p>Sign Up at <a href="https://zipapi.us/" target="_blank">Zipapi.us</a></p>
        <p>
            <label for="api-key">
                <span>API Key</span><br />
                <input type="text" name="zipapi" value="<?php echo $zipapi; ?>" />
            </label>
        </p>
        <p>
            <label for="zip-email">
                <span>Email</span><br />
                <input type="email" name="zipemail" value="<?php echo $zipemail; ?>" />
            </label>
        </p>
        <p>
            <label for="zip-password">
                <span>Password</span><br />
                <input type="password" readonly="readonly" onfocus="this.removeAttribute('readonly');" name="zippassword" value="<?php echo $zippassword; ?>" />
            </label>
        </p>
        <hr>
        <h3>Recaptcha V2</h3>
        <p>Create keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">Google Recaptcha</a></p>
        <p>
            <label for="recaptcha-key">
                <span>Key</span><br />
                <input type="text" name="recapkey" value="<?php echo $recaptchakey; ?>" />
            </label>
        </p>
        <p>
            <label for="recaptcha-secret-key">
                <span>Secret Key</span><br />
                <input type="password" readonly="readonly" onfocus="this.removeAttribute('readonly');" name="recapseckey" value="<?php echo $recaptchasecret; ?>" />
            </label>
        </p>
        <p class="ik_recaptcha_radio_options">
            <label for="recaptcha-option-robot">
                <input type="radio" name="userecaptcha_option" value="Robot" <?php echo $robotchecked; ?> /> V2 - I'm not a Robot
            </label>
            <label for="recaptcha-option-invisible">
                <input type="radio" name="userecaptcha_option" value="Invisible" <?php echo $invisiblechecked; ?> /> V2 - Invisible
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="userecaptcha" <?php echo $recapchacheck; ?> value="1">
                <span>Enable Recaptcha.</span>
            </label>
        </p>
        <hr>
        <p>
            <h3>GoDaddy Email Marketing Form ID</h3>
            <label>
    			<span>* Leave it empty to disable</span><br />
                <input type="text" name="emgdaddy_form_id" placeholder="Insert form_id or leave empty" value="<?php echo $emgdaddy_form_id; ?>" />
            </label>
        </p>

        <p>
            <input type="submit" value="Save" class="button-primary">
        </p>
    </form>
    <script>
        jQuery('#ik_prt_location_map_map_id').val('<?php echo $map_id_saved; ?>');
        jQuery('#ik_prt_location_map_map_icon').val('<?php echo $map_icon_saved; ?>');
    </script>
</div>