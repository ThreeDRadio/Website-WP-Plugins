<?php

class FeatureWidget extends WP_Widget {

	function __construct() {
		parent::__construct('threed_featured_music', 'Featured Music', array('description' => "Displays the album and feature of the week"));
	}

	function widget($args, $instance) {
		extract($args);
		echo $before_widget;
		echo $before_title;
		echo '<img src="';
		bloginfo('template_directory');
		echo '/images/FeaturedMusic.png" alt="Featured Music" class="threed-sidebar-heading"/>';
	   	echo $after_title;

	}
}
add_action( 'widgets_init', create_function( '', 'register_widget( "FeatureWidget" );' ) );
