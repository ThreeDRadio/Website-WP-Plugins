<?php
/**
 * @package Three D Depthcharge 
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Depthcharge
Plugin URI: http://www.20papercups.net
Description: Custom post type for Depthcharge compilations 
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/


// Let's create the custom post type for a publication
add_action('init', 'ThreeDCreateDepthchargeType');


function ThreeDCreateDepthchargeType() {
	register_post_type('threed_depthcharge',
		array(
			'labels' => array(
				'name' => __( 'Depthcharge' ),
				'singular_name' => __( 'Release' ),
				'add_new' => _x('Add New', 'threed_depthcharge'),
				'add_new_item' => __('Add Release'),
				'edit_item' => 'Edit Release',
				'new_item' => 'New Release',
				'view_item' => 'View Release',
				'search_item' => 'Search Depthcharge',
				'not_found' => 'Now releases found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail', 'editor'),
			'rewrite' => array('slug' => 'depthcharge')
		)
	);
}


function threed_depthcharge_shortcode( $atts , $content=null ) {

		$args = array('post_type' => 'threed_depthcharge', 
			          'post_status' => 'publish', 
			          'nopaging' => true,
					  'orderby' => 'date',
					  'order' => 'asc',
				  );

		$albumloop = new WP_Query($args);

		while ($albumloop->have_posts())
		{
			$albumloop->the_post();
			echo '<h2>' . get_the_title() . '</h2>';
		}

}
add_shortcode('depthcharge', 'threed_depthcharge_shortcode');

