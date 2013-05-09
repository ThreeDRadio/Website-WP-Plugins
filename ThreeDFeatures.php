<?php
/**
 * @package Three D Features 
 * @version 0.1
 */
/*
Plugin Name: Three D Radio Featured Music
Plugin URI: http://www.20papercups.net
Description: Album and Feature of the week plugin for Three D Radio Website
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/

require("FeatureWidget.php");

// Let's create the custom post type for a publication
add_action('init', 'ThreeDCreateFeatureType');
add_action('add_meta_boxes', 'ThreeDFeatureAddMetaBoxes');
add_action('save_post', 'ThreeDFeatureSaveMeta');

$featureBox = array (
	'id' => 'threed_feature_meta',
	'title' => "Featured Music Information",
	'page' => 'threed_feature',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array (
		array(
			'name' => 'Type',
			'desc' => '',
			'id' => 'threed_feature_type',
			'type' => 'select',
			'options' => array (0 => 'Feature of the Week', 1 => 'Album of the Week'),
			'std' => ''
		),
		array(
			'name' => 'Artist',
			'desc' => '',
			'id' => 'threed_feature_artist',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Label',
			'desc' => '',
			'id' => 'threed_feature_label',
			'type' => 'text',
			'std' => ''
		)
	)
);

function threedFeatureMetaBox()
{
	global $featureBox, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="threed_feature_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
	foreach ($featureBox['fields'] as $field) {
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

function ThreeDCreateFeatureType() {
	register_post_type('threed_feature',
		array(
			'labels' => array(
				'name' => __( 'Featured Music' ),
				'singular_name' => __( 'Feature' ),
				'add_new' => _x('Add New', 'threed_feature'),
				'add_new_item' => __('Add Featured Music'),
				'edit_item' => 'Edit Feature',
				'new_item' => 'New Feature',
				'view_item' => 'View Feature',
				'search_item' => 'Search Featured Music',
				'not_found' => 'Now features found'
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail', 'editor'),
			'rewrite' => array('slug' => 'features')
		)
	);
}

function ThreeDFeatureAddMetaBoxes() {
	global $featureBox;
	add_meta_box($featureBox['id'], $featureBox['title'], 'ThreeDFeatureMetaBox', 'threed_feature');
}

function ThreeDFeatureSaveMeta($post_id) {
	global $featureBox;
	// verify nonce
	if (!isset($_POST['threed_feature_meta_box_nonce']) || !wp_verify_nonce($_POST['threed_feature_meta_box_nonce'], basename(__FILE__))) {
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
	foreach ($featureBox['fields'] as $field) {
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
