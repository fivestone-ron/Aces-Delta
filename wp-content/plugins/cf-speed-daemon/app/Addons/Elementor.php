<?php

/**
 * The Query Monitor addon.
 * In here we have logic to make the 3rd-party plugin work seamlessly.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Addons;

/**
 * The main class
 */
class Elementor
{
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Elementor
	 */
	private static $instance;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor
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
		$isElementorEditor = filter_input(INPUT_GET, 'elementor-preview');
		if ($isElementorEditor !== null) {
			add_filter('cf_speed_daemon_stop_replace_styles', '__return_true');
		}
	}
}
