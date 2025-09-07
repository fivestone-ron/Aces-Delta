<?php

/**
 * Officer CPT model.
 *
 * @package cf-cpts
 */

namespace CrowdFavorite\CPTs\CPTs;

use CrowdFavorite\CPTs\ModelBase;

/**
 * Officer CPT model.
 */
class Officers extends ModelBase
{
	/**
	 * Model name.
	 *
	 * @access public
	 */
	public const MODEL = 'officer';

	/**
	 * Register the custom post type.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function register()
	{
		$labels = [
			'name' => __('Officers', 'cf-cpts'),
			'singular_name' => __('Officer', 'cf-cpts'),
			'menu_name' => __('Officers', 'cf-cpts'),
			'name_admin_bar' => __('Officer', 'cf-cpts'),
			'parent_item_colon' => __('Parent Officer:', 'cf-cpts'),
			'all_items' => __('All Officers', 'cf-cpts'),
			'add_new_item' => __('Add Officer', 'cf-cpts'),
			'add_new' => __('Add Officer', 'cf-cpts'),
			'new_item' => __('New Officer', 'cf-cpts'),
			'edit_item' => __('Edit Officer', 'cf-cpts'),
			'update_item' => __('Update Officer', 'cf-cpts'),
			'view_item' => __('View Officer', 'cf-cpts'),
			'search_items' => __('Search Officers', 'cf-cpts'),
			'not_found' => __('Not found', 'cf-cpts'),
			'not_found_in_trash' => __('Not found in Trash', 'cf-cpts'),
		];

		register_post_type(
			self::MODEL,
			[
				'labels' => $labels,
				'supports' => [
					'editor',
					'excerpt',
					'title',
					'thumbnail',
					'custom-fields',
				],
				'taxonomies' => [
				],
				'hierarchical' => false,
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 6,
				'menu_icon' => 'dashicons-id-alt',
				'show_in_admin_bar' => true,
				'show_in_nav_menus' => true,
				'can_export' => true,
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'has_archive' => false,
				'rewrite' => [
					'slug' => 'officers',
					'with_front' => true,
				],
				'show_in_rest' => true,
				'capability_type' => 'post',
			]
		);
	}

	/**
	 * Method to exclude current officer from related field
	 *
	 * @access public
	 *
	 * @return array $args Arguments for the relationship query
	 */
	public function excludeSelfFromRelated($args, $field, $post)
	{
		$args['post__not_in'] = [$post];

		return $args;
	}
}
