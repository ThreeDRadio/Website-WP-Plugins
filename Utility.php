<?php

/**
 * @package Three D Radio Utilities 
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Utilities 
Plugin URI: http://www.20papercups.net
Description: Random Wordpress mods to make Three D's website work.
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/


function cwc_mail_shortcode( $atts , $content=null ) {
	$encodedmail = "";
    for ($i = 0; $i < strlen($content); $i++) $encodedmail .= "&#" . ord($content[$i]) . ';'; 
    return '<a href="mailto:'.$encodedmail.'">'.$encodedmail.'</a>';
}
add_shortcode('mailto', 'cwc_mail_shortcode');


?>
