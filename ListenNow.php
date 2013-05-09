<?php

class ListenNow extends WP_Widget {


	function __construct() {
		parent::__construct('threed_listen_now', 'Listen Now', array('description' => "Displays the radio mp3 stream"));
	}

	function widget($args, $instance) {

		extract($args);
		echo $before_widget;
		echo $before_title;
		echo '<img src="';
		bloginfo('template_directory');
		echo '/images/ListenNow.png" alt="Listen Now!" class="threed-sidebar-heading"/>';
	   	echo $after_title;

		echo '<div class="listen_now">';
		echo '<div class="play_button" >';
		echo '<a href="http://media.on.net/radio/137.m3u" style="display:block;width:33px;height:33px;">&nbsp</a>';
		echo '</div>';
		echo '<a href="http://media.on.net/radio/137.m3u">MP3 Stream</a><br />';
		echo '<a href="'. get_page_link('361') .'">Other ways to listen</a>';
		echo '</div>';
		
		echo $after_widget;
	}

};

add_action( 'widgets_init', create_function( '', 'register_widget( "ListenNow" );' ) );

?>
