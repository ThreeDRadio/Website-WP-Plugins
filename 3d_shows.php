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
	$time = explode(':', $time);
	$m = ($time[0] >= 12) ? 'pm' : 'am';
	if ($time[0] == 0)
		$time[0] = 12;
	else if ($time[0] > 12)
		$time[0] = $time[0] - 12;

	return $time[0] . ':' . $time[1] . $m;
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
		echo get_post_meta($id, 'threed_show_day', true);
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
			'options' => array ('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
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
			foreach ($field['options'] as $option) {
				echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
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
			for ($hour=0; $hour <24; $hour++)
			{
				$displayHour = ($hour > 12) ? $hour - 12 : $hour;
				if ($hour == 0)
				{
					$displayHour = "12";
				}
				$displayMeridian = ($hour >= 12) ? 'pm' : 'am';
				echo '<option value="' . $hour . ':00"' , $meta == $hour . ":00" ? ' selected="selected"' : '', '>', $displayHour, ':00', $displayMeridian, '</option>';
				echo '<option value="' . $hour . ':30"', $meta == $hour . ":30" ? ' selected="selected"' : '', '>', $displayHour, ':30', $displayMeridian, '</option>';
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

?>
