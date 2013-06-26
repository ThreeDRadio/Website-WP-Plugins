<?php

class FeatureWidget extends WP_Widget {

	function __construct() {
		parent::__construct('threed_featured_music', 'Featured Music', array('description' => "Displays the album and feature of the week"));
	}

	function widget($args, $instance) {
		$qargs = array('post_type' => 'threed_feature', 
			          'post_status' => 'publish', 
//			          'meta_key' => 'threed_show_day', 
			          'nopaging' => false,
					  'orderby' => 'date',
					  'order' => 'desc',
					  'posts_per_page' => 1,
					  'meta_query' => array(
						  				'relation' => 'AND',
						  				array(
						                    'key' => 'threed_feature_type',
											'value' => '1',
											'compare' => '=='
										),
									)
				  );

		$ploop = new WP_Query($qargs);

		extract($args);
		echo $before_widget;
		echo $before_title;
		echo '<img src="';
		bloginfo('template_directory');
		echo '/images/FeaturedMusic.png" alt="Featured Music" class="threed-sidebar-heading"/>';
	   	echo $after_title;

		while ($ploop->have_posts())
		{
			echo '<div class="threed_feature">';
			echo '<div class="threed_feature_heading">Album of the Week</div>';
			echo '<div class="threed_feature_image">';
			$ploop->the_post();
			if (has_post_thumbnail(get_the_ID())) {
				echo the_post_thumbnail('threed-feature-thumb');
			}
			else {
				echo '<img src="';
				bloginfo('template_directory');
				echo '/images/NoImage.png" alt="No Show Art" width="50" height="50" alt="No Image"/>';
			}
			echo '</div>';
			echo '<span class="threed_feature_artist">' . get_post_meta(get_the_ID(), 'threed_feature_artist', true) . '</span><br>';
			echo '<span class="threed_feature_title">' . get_the_title() . '</span>';
			echo '</div>';
			//echo '<p class="schedule_time">' . threedFriendlyTime( get_post_meta(get_the_ID(), 'threed_show_start', true)) . ' - ' . 
			//	threedFriendlyTime(get_post_meta(get_the_ID(), 'threed_show_end', true)) . '</p>';
		}
		$qargs = array('post_type' => 'threed_feature', 
			          'post_status' => 'publish', 
//			          'meta_key' => 'threed_show_day', 
			          'nopaging' => false,
					  'posts_per_page' => 1,
					  'orderby' => 'date',
					  'order' => 'desc',
					  'meta_query' => array(
						  				'relation' => 'AND',
						  				array(
						                    'key' => 'threed_feature_type',
											'value' => '2',
											'compare' => '=='
										),
									)
				  );

		$ploop = new WP_Query($qargs);
		while ($ploop->have_posts())
		{
			echo '<div class="threed_feature">';
			echo '<div class="threed_feature_heading">Feature of the Week</div>';
			echo '<div class="threed_feature_image">';
			$ploop->the_post();
			if (has_post_thumbnail(get_the_ID())) {
				echo the_post_thumbnail('threed-feature-thumb');
			}
			else {
				echo '<img src="';
				bloginfo('template_directory');
				echo '/images/NoImage.png" alt="No Show Art" width="50" height="50" alt="No Image"/>';
			}
			echo '</div>';
			echo '<span class="threed_feature_artist">' . get_post_meta(get_the_ID(), 'threed_feature_artist', true) . '</span><br>';
			echo '<span class="threed_feature_title">' . get_the_title() . '</span>';
			echo '</div>';
			//echo '<p class="schedule_time">' . threedFriendlyTime( get_post_meta(get_the_ID(), 'threed_show_start', true)) . ' - ' . 
			//	threedFriendlyTime(get_post_meta(get_the_ID(), 'threed_show_end', true)) . '</p>';
		}
		echo $after_widget;
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget( "FeatureWidget" );' ) );
