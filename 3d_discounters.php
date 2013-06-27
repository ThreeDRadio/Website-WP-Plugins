<?php
/**
 * @package Three D Discounters
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Discounters
Plugin URI: http://www.20papercups.net
Description: Discounters plugin for Three D Radio Website
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/


// Let's create the custom post type for a publication
add_action('init', 'threedCreateDiscounterType');


// Create the metadata boxes for the publication post type
add_action('add_meta_boxes', 'threedDiscounterAddMetaBoxes');

// Actually save the custom fields
add_action('save_post', 'threedDiscounterSaveMeta');


add_action('admin_print_scripts', 'threedDiscounterAdminScripts');
add_action('admin_print_styles', 'threedDiscounterAdminStyles');


// get images to show!
add_image_size('admin-list-thumb', 60, 60, false);
add_image_size('discounter-thumb', 80, 80, false);
add_image_size('discounter-big', 180, 180, false);
//add_filter('manage_threed_discounter_posts_columns', 'threedAddDiscounterThumbnailColumn', 5);
//function threedAddDiscounterThumbnailColumn($cols)
//{
//	$temp = array();
//	$temp['cb'] = $cols['cb'];
//	$temp['threed_post_thumb'] = ''; // __('Featured');
//	$temp['title'] = $cols['title'];
//	$temp['threed_show_info'] = 'Show Info';
//	return $temp;
//}


function add_discounter_taxonomies() {
	// Add new "Locations" taxonomy to Posts
	register_taxonomy('threed_discounter_category', 'threed_discounter', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Discounter Category', 'taxonomy general name' ),
			'singular_name' => _x( 'Discounter Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Categories' ),
			'all_items' => __( 'All Categories' ),
			'parent_item' => __( 'Parent Category' ),
			'parent_item_colon' => __( 'Parent Category: ' ),
			'edit_item' => __( 'Edit Category' ),
			'update_item' => __( 'Update Category' ),
			'add_new_item' => __( 'Add New Category' ),
			'new_item_name' => __( 'New Category Name' ),
			'menu_name' => __( 'Discounter Categories' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'locations', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/locations/"
			'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
		),
	));
}
add_action( 'init', 'add_discounter_taxonomies', 0 );


function threedDisplayDiscounterThumbnailColumn($col, $id)
{
	switch($col)
	{
	case 'threed_post_thumb':
		if ( function_exists('the_post_thumbnail'))
			echo the_post_thumbnail('admin-list-thumb');
		else
			echo "not supported";
		break;
	}
}

function threedDiscounterAdminScripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
}

function threedDiscounterAdminStyles() {
	wp_enqueue_style('thickbox');
}


/**
 * Just to make things easier, this array stores all the information
 * for the publication meta data box.
 */
$discounterBox = array (
	'id' => 'threed_discounter_meta',
	'title' => "Discounter Information",
	'page' => 'threed_discounter',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array (
		array(
			'name' => 'Discount Offered',
			'desc' => '',
			'id' => 'threed_discounter_discount',
			'type' => 'textarea',
			'std' => ''
		),
		array(
			'name' => 'Address 1',
			'desc' => '',
			'id' => 'threed_discounter_address1',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Address 2',
			'desc' => '',
			'id' => 'threed_discounter_address2',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Suburb',
			'desc' => '',
			'id' => 'threed_discounter_suburb',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Website',
			'desc' => 'eg http://www.threedradio.com',
			'id' => 'threed_discounter_url',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Email Address',
			'desc' => '',
			'id' => 'threed_discounter_email',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Phone Number',
			'desc' => '',
			'id' => 'threed_discounter_phone',
			'type' => 'text',
			'std' => ''
		)
	)
);


function threedDiscounterPublicationMetaBox()
{
	global $discounterBox, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="threed_discounter_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
	foreach ($discounterBox['fields'] as $field) {
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
function threedCreateDiscounterType() {
	register_post_type('threed_discounter',
		array(
			'labels' => array(
				'name' => __( 'Discounters' ),
				'singular_name' => __( 'Discounter' ),
				'add_new' => _x('Add New', 'threed_discounter'),
				'add_new_item' => __('Add New Discounter'),
				'edit_item' => 'Edit Discounter',
				'new_item' => 'New Discounter',
				'view_item' => 'View Discounter Info',
				'search_item' => 'Search Discounters',
				'not_found' => 'Now discounters found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail', 'editor'),
			'rewrite' => array('slug' => 'discounters')
		)
	);
}


function threedDiscounterAddMetaBoxes() {
	global $discounterBox;
	add_meta_box($discounterBox['id'], $discounterBox['title'], 'threedDiscounterPublicationMetaBox', 'threed_discounter');
}

// Save data from meta box
function threedDiscounterSaveMeta($post_id) {
	global $discounterBox;
	// verify nonce
	if (!isset($_POST['threed_discounter_meta_box_nonce']) || !wp_verify_nonce($_POST['threed_discounter_meta_box_nonce'], basename(__FILE__))) {
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
	foreach ($discounterBox['fields'] as $field) {
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


function threedDiscounterThumbnailColumnWidth()
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

function threedRenderDiscounters()
{
	$args = array('post_type' => 'threed_discounter', 
		'post_status' => 'publish', 
		'nopaging' => true,
		'orderby' => 'title',
		'order' => 'asc',
	);

	$loop = new WP_Query($args);

	while ($loop->have_posts())
	{
		$loop->the_post();
		echo '<header class="entry-header">';
		echo '<h2 class="entry-title">' . get_the_title() . '</h2>';
		echo the_post_thumbnail('discounter-thumb');
		echo '</header>';

	}

}


add_shortcode('threed_discounters', 'threedRenderDiscounters');
?>
