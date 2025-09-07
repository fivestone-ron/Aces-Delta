<?php

/**
 * Model base class.
 *
 * @package cf-cpts
 */

namespace CrowdFavorite\CPTs;

/**
 * Model base class.
 */
abstract class ModelBase
{
	/**
	 * Model base class constructor.
	 *
	 * @access public
	 */
	public function __construct()
	{
		if (method_exists($this, 'register')) {
			add_action('init', [$this, 'registrationHandler'], 0);
		}

		if (method_exists($this, 'registerACFFields')) {
			add_action('init', [$this, 'acfFieldsRegistrationHandler'], 0);
		}
	}

	/**
	 * Method to handle the registration of ACF fields.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function acfFieldsRegistrationHandler()
	{
		if (function_exists('acf_add_local_field_group')) {
			$this->registerACFFields();
		}
	}

	/**
	 * Method to handler registration hooks
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function registrationHandler()
	{
		if (strpos(get_called_class(), 'CrowdFavorite\CPTs\Taxonomies') !== false) {
			// Get the post types to which the taxonomy is attached.
			$object_types = Core::getInstance()->getTaxonomyObjectTypes(static::MODEL);

			// Do not register the taxonomy if it's not attached to anything.
			if ($object_types) {
				$this->register($object_types);
			}
		} else {
			$this->register();
		}
	}
}
