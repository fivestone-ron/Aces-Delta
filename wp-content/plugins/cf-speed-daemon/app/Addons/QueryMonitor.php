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
class QueryMonitor
{
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var QueryMonitor
	 */
	private static $instance;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return QueryMonitor
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
		add_filter('cf_speed_daemon_ignore_handles', [$this, 'ignoreList']);
	}

	/**
	 * Add the plugin css needed.
	 *
	 * @param  array $ignoreList The ignore list.
	 * @return array The new ignore list.
	 */
	public function ignoreList($ignoreList)
	{
		$ignoreList[] = 'query-monitor';
		return $ignoreList;
	}
}
