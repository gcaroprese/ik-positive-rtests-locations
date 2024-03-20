<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Positive R. Test Locations Functions 
Created: 01/21/2022
Last Update: 01/25/2022
Author: Gabriel Caroprese
*/

//Form form_id
function ik_prt_location_map_form(){
    
    $output = '
    <style>
    @font-face {
      font-family: "Poppins";
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/poppins/v15/pxiEyp8kv8JHgFVrJJbecnFHGPezSQ.woff2) format("woff2");
      unicode-range: U+0900-097F, U+1CD0-1CF6, U+1CF8-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FB;
    }
    /* latin-ext */
    @font-face {
      font-family: "Poppins";
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/poppins/v15/pxiEyp8kv8JHgFVrJJnecnFHGPezSQ.woff2) format("woff2");
      unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
    }
    /* latin */
    @font-face {
      font-family: "Poppins";
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/poppins/v15/pxiEyp8kv8JHgFVrJJfecnFHGPc.woff2) format("woff2");
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    }
    /* devanagari */
    @font-face {
      font-family: "Poppins";
      font-style: normal;
      font-weight: 900;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLBT5Z11lFd2JQEl8qw.woff2) format("woff2");
      unicode-range: U+0900-097F, U+1CD0-1CF6, U+1CF8-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FB;
    }
    /* latin-ext */
    @font-face {
      font-family: "Poppins";
      font-style: normal;
      font-weight: 900;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLBT5Z1JlFd2JQEl8qw.woff2) format("woff2");
      unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
    }
    /* latin */
    @font-face {
      font-family: "Poppins";
      font-style: normal;
      font-weight: 900;
      font-display: swap;
      src: url(https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLBT5Z1xlFd2JQEk.woff2) format("woff2");
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    }
    
    #ik_prt_form {
      font-family: poppins;
    }
    
    #ik_prt_form {
      position: relative;
      max-width: 420px;
      border-radius: 20px;
      padding: 60px;
      box-sizing: border-box;
      background: #ecf0f3;
      box-shadow: 14px 14px 20px #cbced1, -14px -14px 20px white;
    }
    #ik_prt_formlabel, #ik_prt_form input, #ik_prt_form button {
      display: block;
      width: 100%;
      padding: 0;
      border: none;
      outline: none;
      box-sizing: border-box;
    }
    
    #ik_prt_form label {
    margin-top: 12px;  
    margin-bottom: 4px;
    }
    
    #ik_prt_form label:nth-of-type(3) {
      margin-top: 30px;
    }
    
    #ik_prt_form input::placeholder {
      color: gray;
    }
    
    #ik_prt_form input {
      background: #ecf0f3;
      padding: 10px;
      padding-left: 20px;
      height: 50px;
      font-size: 14px;
      border-radius: 50px;
      box-shadow: inset 6px 6px 6px #cbced1, inset -6px -6px 6px white;
    }
    
    #ik_prt_submit_form {
      margin-top: 40px;
      background: #1DA1F2;
      height: 40px;
      border-radius: 20px;
      cursor: pointer;
      font-weight: 900;
      color: white;
      box-shadow: 6px 6px 6px #cbced1, -6px -6px 6px white;
      transition: 0.5s;
    }
    #ik_prt_message_form {
        font-size: 17px;
        margin-top: 20px;
        text-align: center;
    }
    #ik_prt_form .g-recaptcha {
        margin-top: 20px;
        margin-bottom: -15px;
    }
    #ik_prt_submit_form:hover {
      box-shadow: none;
    }
    #ik_prt_submit_form.sending_data:after{
        content: "";
        background: url('.IK_PRT_LOCATION_MAP_PUBLIC.'/img/loading.gif);;
        width: 24px;
        height: 24px;
        background-size: cover;
        display: inline-flex;
        position: relative;
        top: 5px;
        right: -2px;
    }
    #ik_prt_form .form_image_top{
        text-align: center;
    }
    #ik_prt_form_emailgodaddy{
        display: none! important;
        opacity: 0! important;
        visibility: hidden;
    }
    </style>

    <div name="ik_prt_form"  id="ik_prt_form">
        <img src="'.IK_PRT_LOCATION_MAP_PUBLIC.'/img/form_title.png" class="form_image_top" alt="counted positive cases" />
        <input type="hidden" id="ik_prt_city" name="ik_prt_city" value="">
        <input type="hidden" id="ik_prt_state" name="ik_prt_state" value="">
        <label>U.S. Zip Code <span style="color: red"> *</span></label>
        <input required type="text" id="ik_prt_zip_code" name="ik_prt_zip_code" pattern="[0-9]{5}" min="5" max="5" maxlength="5" placeholder="ex. 10022">
        <label># of People Positive <span style="color: red"> *</span></label>
        <input required type="text" id="ik_prt_positive_cases" name="ik_prt_positive_cases" min="1" max="6" maxlength="1" placeholder="max 6">
        <label>Phone (optional)</label>
        <input type="tel" id="ik_prt_phone" name="ik_prt_phone" placeholder="(555) 555-5555">
        <label>Email updates (optional)</label>
        <input type="email" id="ik_prt_email" name="ik_prt_email" placeholder="ex. joe@joe.com">
        '.ik_prt_location_map_get_recaptcha_form().'
        <button type="submit" id="ik_prt_submit_form">SUBMIT</button>
        <div id="ik_prt_message_form"></div>
    </div>';
    
    //I add GoDaddy Email Marketing shortcode if enabled
    $emgdaddy_form_id = get_option('ik_prt_location_emgdaddy_form_id');
    if ($emgdaddy_form_id !== false && $emgdaddy_form_id !== NULL && $emgdaddy_form_id !== ''){
        $shortcode_gdaddy = '[gem id="'.$emgdaddy_form_id.'"]';
        $output .= '<div id="ik_prt_form_emailgodaddy">'.do_shortcode($shortcode_gdaddy).'</div>';
    }

    return $output;
    
}
add_shortcode('PRT_FORM', 'ik_prt_location_map_form');