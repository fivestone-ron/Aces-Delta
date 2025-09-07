<?php

/**
 * Manages the plugin addons.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Addons;

/**
 * The main class
 */
class Addons
{
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Addons
	 */
	private static $instance;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return Admin
	 */
	public static function getInstance()
	{
		if (! self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * The main class construct.
	 */
	public function __construct()
	{
		$buildInAddons = [
			'QueryMonitor',
			'BeaverBuilder',
			'Elementor'
		];

		foreach ($buildInAddons as $className) {
			call_user_func([__NAMESPACE__ . '\\' . $className, 'getInstance']);
		}
	}
}
