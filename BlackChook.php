<?php
/**
 * @package Black Chook Blues Discount
 * @version 0.1
 */
/*
Plugin Name: Black Chook Blues Discount
Plugin URI: http://www.20papercups.net
Description: Ninja Form Customisation for black chook
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
 */


function add_change_ninja_forms_landing_page(){
    add_action( 'ninja_forms_pre_process', 'change_ninja_forms_landing_page' );
}
add_action( 'init', 'add_change_ninja_forms_landing_page' );

function change_ninja_forms_landing_page(){
    global $ninja_forms_processing; // The global variable gives us access to all the form and field settings.
    global $wpdb;

    $form_id = $ninja_forms_processing->get_form_ID(); // Gets the ID of the form we are currently processing.
    if( $form_id == 2 ){ // Check to make sure that this form has the same ID as the one we got earlier.
        $location = $ninja_forms_processing->get_field_value( 1086 ); // Gets the value that the user has submitted.

        $name = $ninja_forms_processing->get_field_value(6);
        $number = $ninja_forms_processing->get_field_value(7);

        if (!empty($name) && !empty($number)) {

            $rows = $wpdb->get_var("SELECT COUNT(*) FROM subscribers WHERE card='$number' AND name='$name'");

            if ($rows == 0) {
                $ninja_forms_processing->add_error('Three D FAIL', '<p>I\'m sorry, but we can\'t find your subscriber records. If you believe this is an error, please email <a href="mailto:secretary@threedradio.com">secretary@threedradio.com</a></p>');
            }
        }
    }
}
