<?php
/**
 * @package Three D Top 100 + 1 
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Top 100+1
Plugin URI: http://www.20papercups.net
Description: Support for Three D Radio Top 101 Charts
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/

add_action('init', 'ThreeDCreateTop101EntryType');
add_action('init', 'ThreeDCreateTop101ChartType');

add_action('add_meta_boxes', 'threedTop101AddMetaBoxes');

add_action('save_post', 'threedTop101SaveMeta');
add_action('save_post', 'threedTop101EntrySaveMeta');

add_filter('name_save_pre', 'ThreeDTop101SaveName');
add_filter('title_save_pre','ThreeDTop101SaveName');

add_filter('name_save_pre', 'ThreeDTop101EntrySaveName');
add_filter('title_save_pre','ThreeDTop101EntrySaveName');

function ThreeDTop101SaveName($my_post_name) {
	if (isset ($_POST['post_type']) && $_POST['post_type'] == 'threed_top101_chart') {
		$name = 'Top 100+1 for ' . $_POST['threed_top101_year'];
		$my_post_name = $name;
	}
	return $my_post_name;
}

function ThreeDTop101EntrySaveName($my_post_name) {
	if (isset ($_POST['post_type']) && $_POST['post_type'] == 'threed_top101_entry') {
		$name = $_POST['threed_top101_position'] . ': ' . $_POST['threed_top101_artist'] . ' - ' . $_POST['threed_top101_release'];
		$my_post_name = $name;
	}
	return $my_post_name;
}

function ThreeDCreateTop101ChartType() {
	global $wp_rewrite;
	register_post_type('threed_top101_chart',
		array(
			'labels' => array(
				'name' => __( 'Top 101 Charts' ),
				'singular_name' => __( 'Chart' ),
				'add_new' => _x('Add New', 'threed_top101_chart'),
				'add_new_item' => __('Add Chart'),
				'edit_item' => 'Edit Chart',
				'new_item' => 'New Chart',
				'view_item' => 'View Chart',
				'search_item' => 'Search Top 100+1',
				'not_found' => 'No charts found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array('comments','editor'),
			'rewrite' => array('slug' => 'top101')
		)
	);
	$wp_rewrite->flush_rules(); 
}

function ThreeDCreateTop101EntryType() {
	global $wp_rewrite;
	register_post_type('threed_top101_entry',
		array(
			'labels' => array(
				'name' => __( 'Top 101 Entries' ),
				'singular_name' => __( 'Entry' ),
				'add_new' => _x('Add New', 'threed_top101_entry'),
				'add_new_item' => __('Add Entry'),
				'edit_item' => 'Edit Entry',
				'new_item' => 'New Entry',
				'view_item' => 'View Entry',
				'search_item' => 'Search Top 100+1',
				'not_found' => 'No entries found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array('thumbnail'),
			'rewrite' => array('slug' => 'top101')
		)
	);
	$wp_rewrite->flush_rules(); 
}

function ThreeDTop101AddMetaBoxes() {
    add_meta_box('threed_top101_meta', 'Chart Information', 'ThreeDTop101PrintMetaBox', 'threed_top101_chart');
    add_meta_box('threed_top101_entries_list', 'Chart Entries', 'ThreeDTop101PrintEntriesBox', 'threed_top101_chart');
    add_meta_box('threed_top101_entry_meta', 'Entry Information', 'ThreeDTop101EntryPrintMetaBox', 'threed_top101_entry');
}

function ThreeDTop101PrintMetaBox() {
	global $post;
	$year= get_post_meta($post->ID,   "threed_top101_year", true);
	echo '<input type="hidden" name="threed_top101_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
    echo '<tr>';
    echo '<td>Chart Year</td>';
    echo '<td><input type="text" name="threed_top101_year"  id="threed_top101_year" value="', $year, '" size="25"/></td>';
    echo '</tr></table>';
}

function ThreeDTop101PrintEntriesBox() {
	$args = array('post_type' => 'threed_top101_entry', 
		'post_status' => 'publish', 
		'nopaging' => true,
		'orderby' => 'meta_value_num',
		'meta_key' => 'threed_top101_position', 
		'order' => 'asc',
	);

	$entries = new WP_Query($args);

	echo '<table class="form-table">';
    echo '<tr><th>Pos</th><th>Artist</th><th>Release</th></tr>';

	while ($entries->have_posts())
	{
		$entries->the_post();
        echo '<tr><td>' . get_post_meta(get_the_ID(), "threed_top101_position", true) . '</td>';
        echo '<td>' . get_post_meta(get_the_ID(), "threed_top101_artist", true) . '</td>';
        echo '<td>' . get_post_meta(get_the_ID(), "threed_top101_release", true) . '</td></tr>';
	}
    echo '</table>';
}

function ThreeDTop101EntryPrintMetaBox() {

	$args = array('post_type' => 'threed_top101_chart', 
		'post_status' => 'publish', 
		'nopaging' => true,
		'orderby' => 'title',
		'order' => 'asc',
	);

	$charts = new WP_Query($args);
    
	global $post;
	$chart = get_post_meta($post->ID,   "threed_top101_chart_parent", true);
	$artist = get_post_meta($post->ID,   "threed_top101_artist", true);
	$release = get_post_meta($post->ID,   "threed_top101_release", true);
	$position = get_post_meta($post->ID,   "threed_top101_position", true);

	echo '<input type="hidden" name="threed_top101_entry_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
    echo '<tr>';
    echo '<td>Chart</td>';
    echo '<td>';
	echo '<select name="threed_top101_chart_parent">';
	while ($charts->have_posts())
	{
		$charts->the_post();
        echo '<option value="'. get_the_ID() .'">'. get_post_meta(get_the_ID(), "threed_top101_year", true) . '</option>';
		// echo '<header class="entry-header">';
		// echo '<h2 class="entry-title">' . get_the_title() . '</h2>';
		// echo the_post_thumbnail('discounter-thumb');
		// echo '</header>';

	}
    echo '</select>';

    echo '</td>';
    echo '</tr><tr>';
    echo '<tr>';
    echo '<td>Chart position</td>';
    echo '<td><input type="text" name="threed_top101_position"  id="threed_top101_position" value="', $position, '" size="10"/></td>';
    echo '</tr><tr>';
    echo '<td>Artist</td>';
    echo '<td><input type="text" name="threed_top101_artist"  id="threed_top101_artist" value="', $artist , '" size="45"/></td>';
    echo '</tr><tr>';
    echo '<td>Release</td>';
    echo '<td><input type="text" name="threed_top101_release"  id="threed_top101_release" value="', $release, '" size="45"/></td>';
    echo '</tr></table>';
}

function ThreedTop101EntryMeta($post_id) {
	// verify nonce
	if (!isset($_POST['threed_top101_meta_box_nonce']) || !wp_verify_nonce($_POST['threed_top101_meta_box_nonce'], basename(__FILE__))) {
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

	$year =  $_POST["threed_top101_year"];
	update_post_meta($post_id, "threed_top101_year", $year);
}

function ThreedTop101EntrySaveMeta($post_id) {
	// verify nonce
	if (!isset($_POST['threed_top101_entry_meta_box_nonce']) || !wp_verify_nonce($_POST['threed_top101_entry_meta_box_nonce'], basename(__FILE__))) {
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

	$chart =  $_POST["threed_top101_chart_parent"];
	$position=  $_POST["threed_top101_position"];
	$artist =  $_POST["threed_top101_artist"];
	$release =  $_POST["threed_top101_release"];

	update_post_meta($post_id, "threed_top101_chart_parent", $chart);
	update_post_meta($post_id, "threed_top101_position", $position);
	update_post_meta($post_id, "threed_top101_artist", $artist);
	update_post_meta($post_id, "threed_top101_release", $release);
}

