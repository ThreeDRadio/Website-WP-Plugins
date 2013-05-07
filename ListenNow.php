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

		echo $after_widget;
	}

};

add_action( 'widgets_init', create_function( '', 'register_widget( "ListenNow" );' ) );

?>
