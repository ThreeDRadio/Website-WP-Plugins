<?php

class NowPlaying extends WP_Widget {


	function __construct() {
		parent::__construct('threed_now_playing', 'Now Playing', array('description' => "Displays the current show"));
	}

	function widget($args, $instance) {
		$currentTime = time();
		$tz = 'Australia/Adelaide';
		$timezone = new DateTimeZone($tz);
		$dt = new DateTime();
		$dt->setTimestamp($currentTime);
		$dt->setTimeZone($timezone);

		$time = $dt->format('U');

		$day = $dt->format('N') - 1;
		$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		$secondsSinceMidnight = $dt->format('G')*60*60 + $dt->format('i') * 60 + $dt->format('s');

		$qargs = array('post_type' => 'threed_show', 
			          'post_status' => 'publish', 
			          'meta_key' => 'threed_show_day', 
			          'nopaging' => true,
					  'orderby' => 'meta_value_num',
					  'order' => 'asc',
					  'meta_query' => array(
						  				'relation' => 'AND',
						  				array(
						                    'key' => 'threed_show_start',
											'value' => $secondsSinceMidnight,
											'compare' => '<='
										),
										array(
						                    'key' => 'threed_show_end',
											'value' => $secondsSinceMidnight,
											'compare' => '>='
										    ),
										array(
						                    'key' => 'threed_show_day',
											'value' => $day,
											'compare' => '='
										    )
									)
				  );

		$ploop = new WP_Query($qargs);

		extract($args);
		echo $before_widget;
		echo $before_title;
		echo '<img src="';
		bloginfo('template_directory');
		echo '/images/NowPlaying.png" alt="Now Playing" class="threed-sidebar-heading"/>';
	   	echo $after_title;

	echo '
		<style>
		.threed_schedule {
			width: 100%;
}
		.threed_schedule td {
			border-width: 1px;
			border-style: solid;
			width: 14.2%;
} 

.threed_schedule p {
	margin: 0px;
}
.schedule_show {
	font-weight: bold;
	padding-bottom: 3px;
	margin: 0px;
	padding: 0px;
	text-align: center;
}
.schedule_show a {
	color: #333;
	text-decoration: none;

}
.schedule_show a:hover {
	color: #0f0;
}
.schedule_time {
	font-size: 7pt;
	margin: 0px;
	padding: 0px;
	padding-left: 2em;
}

.now_playing {
	background-color: #ccc;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 35px;
	padding-bottom: 5px;
	margin-left: 12px;
	width: 230px;
	border-radius: 5px;
	text-align: center;
	font-size: 9pt;
	position: relative;
	top: -24px;
	z-index: 0;
}
</style>';

		while ($ploop->have_posts())
		{
			echo '<div class="now_playing">';
			$ploop->the_post();
			if (has_post_thumbnail(get_the_ID())) {
				echo the_post_thumbnail('threed-now-playing');
			}
			else {
				echo '<img src="';
				bloginfo('template_directory');
				echo '/images/NoImage.png" alt="No Show Art"/>';
			}
			echo '<span class="schedule_show"><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></span>';
			echo ' with ' . get_post_meta(get_the_ID(), 'threed_show_hosts', true);
			echo '</div>';
			//echo '<p class="schedule_time">' . threedFriendlyTime( get_post_meta(get_the_ID(), 'threed_show_start', true)) . ' - ' . 
			//	threedFriendlyTime(get_post_meta(get_the_ID(), 'threed_show_end', true)) . '</p>';
		}
		echo $after_widget;
	}

};

add_action( 'widgets_init', create_function( '', 'register_widget( "NowPlaying" );' ) );

?>
