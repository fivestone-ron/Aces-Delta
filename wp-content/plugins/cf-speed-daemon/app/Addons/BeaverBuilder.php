<?php

/**
 * The Beaver Builder addon.
 * In here we have logic to make the 3rd-party plugin work seamlessly.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Addons;

/**
 * The main class
 */
class BeaverBuilder
{
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var BeaverBuilder
	 */
	private static $instance;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return BeaverBuilder
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
		$isBeaverBuilderEditor = filter_input(INPUT_GET, 'fl_builder');
		if ($isBeaverBuilderEditor !== null) {
			add_filter('cf_speed_daemon_stop_replace_styles', '__return_true');
			add_filter('cf_speed_daemon_remove_url_args', [$this, 'removeUrlArgsHook']);
		}
	}

	/**
	 * Remove un-needed url args.
	 * Used to generate correct url in admin bar for the manual css purge link.
	 * @param array $urlArgsToRemove Array of url args to remove from url.
	 * @return array Updated array of url args to remove.
	 */
	public function removeUrlArgsHook(array $urlArgsToRemove): array
	{
		$urlArgsToRemove[] = 'fl_builder';
		return $urlArgsToRemove;
	}
}
