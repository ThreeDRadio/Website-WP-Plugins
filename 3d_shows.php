<?php
/**
 * @package Three D Shows 
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Show Profiles
Plugin URI: http://www.20papercups.net
Description: Show Profiles plugin for Three D Radio Website
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/

require("NowPlaying.php");
require("ListenNow.php");

// Let's create the custom post type for a publication
add_action('init', 'threedCreateShowType');


// Create the metadata boxes for the publication post type
add_action('add_meta_boxes', 'threedShowAddMetaBoxes');

// Actually save the custom fields
add_action('save_post', 'threedShowSaveMeta');


add_action('admin_print_scripts', 'threedShowAdminScripts');
add_action('admin_print_styles', 'threedShowAdminStyles');


// get images to show!
add_image_size('admin-list-thumb', 60, 60, false);
add_image_size('threed-now-playing', 230, 230, false);
add_filter('manage_threed_show_posts_columns', 'threedAddPostThumbnailColumn', 5);
function threedAddPostThumbnailColumn($cols)
{
	$temp = array();
	$temp['cb'] = $cols['cb'];
	$temp['threed_post_thumb'] = ''; // __('Featured');
	$temp['title'] = $cols['title'];
	$temp['threed_show_info'] = 'Show Info';
	return $temp;
}

add_action('manage_posts_custom_column', 'threedDisplayPostThumbnailColumn', 5, 2);
add_action('admin_head', 'threedThumbnailColumnWidth');


function threedFriendlyTime($time)
{
	//$time = explode(':', $time);
	//$m = ($time[0] >= 12) ? 'pm' : 'am';
	//if ($time[0] == 0)
	//	$time[0] = 12;
	//else if ($time[0] > 12)
	//	$time[0] = $time[0] - 12;

	//return $time[0] . ':' . $time[1] . $m;
	$val = strftime('%k:%M', $time);

	if (empty($val)) {
		$val = strftime('%I:%M%p', $time);
	}

	//return $time;
	return $val;
}

function threedGetDay($index)
{
	$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	return $days[$index];
}

function threedDisplayPostThumbnailColumn($col, $id)
{
	switch($col)
	{
	case 'threed_post_thumb':
		if ( function_exists('the_post_thumbnail'))
			echo the_post_thumbnail('admin-list-thumb');
		else
			echo "not supported";
		break;
	case 'threed_show_info':
		echo threedGetDay(get_post_meta($id, 'threed_show_day', true));
		echo ', ' . threedFriendlyTime(get_post_meta($id, 'threed_show_start', true)); 
		echo ' to ' .  threedFriendlyTime(get_post_meta($id, 'threed_show_end', true)); 
		echo '<br>';
		echo 'Hosted by: ' . get_post_meta($id, 'threed_show_hosts', true);
		break;
	}
}

function threedShowAdminScripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('threedShowUpload');
}

function threedShowAdminStyles() {
	wp_enqueue_style('thickbox');
}


/**
 * Just to make things easier, this array stores all the information
 * for the publication meta data box.
 */
$showBox = array (
	'id' => 'threed_show_meta',
	'title' => "Show Information",
	'page' => 'threed_show',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array (
		array(
			'name' => 'Hosted By',
			'desc' => '',
			'id' => 'threed_show_hosts',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Airs On',
			'desc' => '',
			'id' => 'threed_show_day',
			'type' => 'select',
			'options' => array (0 => 'Monday', 1 => 'Tuesday', 2 => 'Wednesday', 3 => 'Thursday', 4 => 'Friday', 5 => 'Saturday', 6 =>'Sunday'),
			'std' => ''
		),
		array(
			'name' => 'Start Time',
			'desc' => '',
			'id' => 'threed_show_start',
			'type' => 'time',
			'std' => ''
		),
		array(
			'name' => 'End Time',
			'desc' => '',
			'id' => 'threed_show_end',
			'type' => 'time',
			'std' => ''
		),
		array(
			'name' => 'Show Blog/Website',
			'desc' => 'eg http://www.threedradio.com',
			'id' => 'threed_show_url',
			'type' => 'text',
			'std' => ''
		),
	)
);


function threedShowPublicationMetaBox()
{
	global $showBox, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="threed_show_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
	foreach ($showBox['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		echo '<tr>',
			'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
			'<td>';
		switch ($field['type']) {
		case 'text':
			echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
			break;
		case 'textarea':
			echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
			break;
		case 'select':
			echo '<select name="', $field['id'], '" id="', $field['id'], '">';
			foreach ($field['options'] as $key => $value) {
				echo '<option value="', $key, '" ', $meta == $key ? ' selected="selected"' : '', '>', $value, '</option>';
			}
			echo '</select>';
			break;
		case 'radio':
			foreach ($field['options'] as $option) {
				echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
			}
			break;
		case 'checkbox':
			echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
			break;
		case 'button':
			echo '<input type="button" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" />';
			break;

		case 'time':
			echo '<select name="', $field['id'], '" id="', $field['id'], '">';
			$startTime = 6*60*60;
			$endTime = 27*60*60;
			$increment = 30*60;

			for ($i = $startTime; $i <= $endTime; $i+=$increment)
			{
				echo '<option value="' . $i . '"' , $meta == $i ? ' selected="selected"' : '', '>', strftime("%r", $i) , '</option>';
			}
			echo '</select>';
			break;
		}
		echo '</td><td>',
			'</td></tr>';
	}
	echo '</table>';
}


/**
 * Registers the Publication post type.
 */
function threedCreateShowType() {
	register_post_type('threed_show',
		array(
			'labels' => array(
				'name' => __( 'Show Profiles' ),
				'singular_name' => __( 'Show' ),
				'add_new' => _x('Add New', 'threed_show'),
				'add_new_item' => __('Add New Show'),
				'edit_item' => 'Edit Show Profile',
				'new_item' => 'New Show',
				'view_item' => 'View Show Profile',
				'search_item' => 'Search Shows',
				'not_found' => 'Now shows found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail', 'editor'),
			'rewrite' => array('slug' => 'shows')
		)
	);
}


function threedShowAddMetaBoxes() {
	global $showBox;
	add_meta_box($showBox['id'], $showBox['title'], 'threedShowPublicationMetaBox', 'threed_show');
}

// Save data from meta box
function threedShowSaveMeta($post_id) {
	global $showBox;
	// verify nonce
	if (!isset($_POST['threed_show_meta_box_nonce']) || !wp_verify_nonce($_POST['threed_show_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}
	foreach ($showBox['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
		if ($field['type'] == "author")
		{
			$old = get_post_meta($post_id, $field['id'] . "_preset", true);
			$new = $_POST[$field['id'] . "_preset"];
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'] . "_preset", $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'] . "_preset", $old);
			}
		}
	}
}


function threedThumbnailColumnWidth()
{
	echo '<style>
		.column-threed_post_thumb {
			width: 80px;
			text-align: center;
		}
		.column-threed_show_info{
			width: 60%;
		}
		</style>';
}

function threedRenderSchedule()
{

	echo '
		<style>
		.threed_schedule {
			width: 100%;
}
		.threed_schedule td {
			border-width: 1px;
			border-style: solid;
			width: 14.2%;
			height: 80px;

			.threed_schedule table {
				width: 100%;
}
} 

.threed_schedule p {
	margin: 0px;
}
.schedule_show {
	font-weight: bold;
	padding-bottom: 3px;
	margin: 0px;
	padding: 0px;
	padding-left: 0.5em;
	font-size: 8pt;
}
.schedule_time {
	font-size: 7pt;
	margin: 0px;
	padding: 0px;
	padding-left: 2em;
}
</style>';

	print '<table class="threed_schedule" width="100%">';
	print '<tr>';

	$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	foreach ($days as $day) {
		print "<th>$day</th>";
	}
	print '</tr>';

	$startTime = 6*60*60;
	$endTime = 26*60*60 + 30*60;
	$increment = 30*60;

	for ($i=$startTime; $i <= $endTime; $i += $increment)
	{
		$friendly = strftime("%k:%M", $i);

		$args = array('post_type' => 'threed_show', 
			          'post_status' => 'publish', 
			          'meta_key' => 'threed_show_day', 
			          'nopaging' => true,
					  'orderby' => 'meta_value_num',
					  'order' => 'asc',
					  'meta_query' => array(array(
						                    'key' => 'threed_show_start',
											'value' => $i,
											'compare' => '='
										))
				  );

		$loop = new WP_Query($args);

		echo '<tr>';


		while ($loop->have_posts())
		{
			$loop->the_post();
			$blocks = get_post_meta(get_the_ID(), 'threed_show_end', true) - get_post_meta(get_the_ID(), 'threed_show_start', true);
			$blocks /= $increment;
			echo '<td rowspan="' . $blocks . '"><p class="schedule_show"><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></p>';
			echo '<p class="schedule_time">' . threedFriendlyTime( get_post_meta(get_the_ID(), 'threed_show_start', true)) . ' - ' . 
				threedFriendlyTime(get_post_meta(get_the_ID(), 'threed_show_end', true)) . '</p>';
			echo '<p class="schedule_time">' . get_post_meta(get_the_ID(), 'threed_show_hosts', true). '</p>';
			echo '</td>';
		}
		echo '</tr>';
	}

	print '</table>';
}

add_shortcode('threed_shows', 'threedRenderSchedule');

?>
