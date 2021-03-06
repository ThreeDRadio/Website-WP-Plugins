<?php
/**
 * @package Three D Top 20 + 1 
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Top 20+1
Plugin URI: http://www.20papercups.net
Description: Support for Three D Radio Charts
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/


// Let's create the custom post type for a publication
add_action('init', 'ThreeDCreateTop20Type');
add_action('add_meta_boxes', 'ThreeDTop20AddMetaBoxes');
add_action('save_post', 'ThreeDTop20SaveMeta'); 


function threedTop20MetaBox()
{
	global $post;
	$day = get_post_meta($post->ID,   "threed_day", true);
	$month = get_post_meta($post->ID, "threed_month", true);
	$year= get_post_meta($post->ID,   "threed_year", true);

	// Use nonce for verification
	echo '<input type="hidden" name="threed_top20_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<h2>Chart for week ending:</h2>';

	echo '<select name="threed_month">';
	echo '<option value="1" ' ,  ($month == 1 ?  ' selected="selected"' : '') , '>January</option>';
	echo '<option value="2" ' ,  ($month == 2 ?  ' selected="selected"' : '') , '>Febuary</option>';
	echo '<option value="3" ' ,  ($month == 3 ?  ' selected="selected"' : '') , '>March</option>';
	echo '<option value="4" ' ,  ($month == 4 ?  ' selected="selected"' : '') , '>April</option>';
	echo '<option value="5" ' ,  ($month == 5 ?  ' selected="selected"' : '') , '>May</option>';
	echo '<option value="6" ' ,  ($month == 6 ?  ' selected="selected"' : '') , '>June</option>';
	echo '<option value="7" ' ,  ($month == 7 ?  ' selected="selected"' : '') , '>July</option>';
	echo '<option value="8" ' ,  ($month == 8 ?  ' selected="selected"' : '') , '>August</option>';
	echo '<option value="9" ' ,  ($month == 9 ?  ' selected="selected"' : '') , '>September</option>';
	echo '<option value="10" ' , ($month == 10 ? ' selected="selected"' : '') , '>October</option>';
	echo '<option value="11" ' , ($month == 11 ? ' selected="selected"' : '') , '>November</option>';
	echo '<option value="12" ' , ($month == 12 ? ' selected="selected"' : '') , '>December</option>';
	echo '</select>';

	echo '<select name="threed_day">';
	for ($d = 1; $d <=31; $d++) {
		echo '<option value="'. $d . '" ', ($day == $d ?  ' selected="selected"' : '') , ' >' . $d .'</option>';
	}
	echo '</select>';

	echo '<select name="threed_year">';
	for ($d = 2012; $d <=2015; $d++) {
		echo '<option value="'. $d . '" ', ($year == $d ?  ' selected="selected"' : '') , ' >' . $d .'</option>';
	}
	echo '</select>';

	echo '<h2>Entries:</h2>';
	echo '<table class="form-table">';
	echo '<tr><th>#</th><th>Artist</th><th>Release</th><th>Origin</th><th>LW</th></tr>';
	for ($i = 1; $i<=21; $i++) {
		$artist = get_post_meta($post->ID, "artist$i", true);
		$release = get_post_meta($post->ID, "release$i", true);
		$origin = get_post_meta($post->ID, "origin$i", true);
		$lastweek= get_post_meta($post->ID, "lastweek$i", true);

		echo '<tr>';
		echo '<td width="2">' . $i . '</td>';
		echo '<td><input type="text" name="artist', $i , '"  id="artist', $i, '" value="', $artist, '" size="25"/></td>';
		echo '<td><input type="text" name="release', $i , '" id="release', $i, '" value="', $release, '" size="25"/></td>';

		echo '<td width="100">';
		echo '<input type="radio" name="origin', $i, '" id="Local" value="Local"',         $origin == "Local"         ? ' checked="checked"' : '', ' /><label for="Local"> Local</label><br>';
		echo '<input type="radio" name="origin', $i, '" id="Australian" value="Australian"',    $origin == "Australian"    ? ' checked="checked"' : '', ' /><label for="Australian"> Aus</label><br>';
		echo '<input type="radio" name="origin', $i, '" id="International" value="International"', $origin == "International" ? ' checked="checked"' : '', ' /><label for="International"> Int</label></td>';

		echo '<td><input type="text" name="lastweek', $i , '" id="lastweek', $i, '" value="', $lastweek, '" size="5"/></td>';

		echo '<tr>';
	}
	echo '</table>';
}

function ThreeDCreateTop20Type() {
	global $wp_rewrite;
	register_post_type('threed_top20',
		array(
			'labels' => array(
				'name' => __( 'Top 20+1' ),
				'singular_name' => __( 'Chart' ),
				'add_new' => _x('Add New', 'threed_top20'),
				'add_new_item' => __('Add Chart'),
				'edit_item' => 'Edit Chart',
				'new_item' => 'New Chart',
				'view_item' => 'View Chart',
				'search_item' => 'Search Top 20+1',
				'not_found' => 'No charts found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array('comments'),
			'rewrite' => array('slug' => 'top20')
		)
	);
	$wp_rewrite->flush_rules(); 
}

add_filter('name_save_pre', 'save_name');
add_filter('title_save_pre', 'save_name');
function save_name($my_post_name) {
	if (isset ($_POST['post_type']) && $_POST['post_type'] == 'threed_top20') {
		$name = $_POST['threed_year'];
		$name .= '-' . $_POST['threed_month'];
		$name .= '-' . $_POST['threed_day'];
		$my_post_name = $name;
	}
	return $my_post_name;
}


function ThreeDTop20AddMetaBoxes() {
	add_meta_box("top20info", "Top 20+1", 'ThreeDTop20MetaBox', 'threed_top20');
}

function ThreeDTop20SaveMeta($post_id) {
	// verify nonce
	if (!isset($_POST['threed_top20_meta_box_nonce']) || !wp_verify_nonce($_POST['threed_top20_meta_box_nonce'], basename(__FILE__))) {
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

	$day = $_POST["threed_day"];
	$month = $_POST["threed_month"];
	$year =  $_POST["threed_year"];
	update_post_meta($post_id, "threed_day", $day);
	update_post_meta($post_id, "threed_month", $month);
	update_post_meta($post_id, "threed_year", $year);

	for ($i = 1; $i<=21; $i++) {
		$artist = $_POST["artist$i"];
		$release= $_POST["release$i"];
		$origin = (isset($_POST["origin$i"])) ? $_POST["origin$i"] : '';
		$lastweek= $_POST["lastweek$i"];

		update_post_meta($post_id, "artist$i", $artist);
		update_post_meta($post_id, "release$i", $release);
		update_post_meta($post_id, "origin$i", $origin);
		update_post_meta($post_id, "lastweek$i", $lastweek);

	}
}

add_filter( 'wp_get_nav_menu_items', 'replace_placeholder_nav_menu_item_with_top20', 10, 3 );
 
// Replaces a custom URL placeholder with the URL to the latest post
function replace_placeholder_nav_menu_item_with_top20( $items, $menu, $args ) {

	// Loop through the menu items looking for placeholder(s)
	foreach ( $items as $item ) {

		// Is this the placeholder we're looking for?
		if ( strpos($item->url, '#top20') === false )
			continue;

		// Get the latest post
		$latestpost = get_posts( array(
			'numberposts' => 1,
			'post_type' => 'threed_top20'
		) );

		if ( empty( $latestpost ) )
			continue;

		// Replace the placeholder with the real URL
		$item->url = get_permalink( $latestpost[0]->ID );
	}

	// Return the modified (or maybe unmodified) menu items array
	return $items;
}

?>
