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

add_image_size('threed-depthcharge-image', 230, 230, false);

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

		$r = "";
		$args = array('post_type' => 'threed_depthcharge', 
			          'post_status' => 'publish', 
			          'nopaging' => true,
					  'orderby' => 'date',
					  'order' => 'desc',
				  );

		$albumloop = new WP_Query($args);


		while ($albumloop->have_posts())
		{
			$albumloop->the_post();
			$r .= '<h2>' . get_the_title() . '</h2>';
			$r .= '<div class="depthcharge_release">';
			$r .= '<div class="depthcharge_thumbnail">';
			$r .= get_the_post_thumbnail(get_the_ID(), 'threed-depthcharge-image');
			$r .= '</div>';
			$r .= '<div class="depthcharge_track_listing">';
			$r .= get_the_content(get_the_ID());
			$r .= '</div>';
			$r .= '</div>';
		}

		return $r;
}
add_shortcode('depthcharge', 'threed_depthcharge_shortcode');

