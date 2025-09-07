<?php

/**
 * This class handles stats calculations for css passed through REST AAPI endpoint.
 *
 * @package cf-speed-daemon
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

/**
 * Class Stats
 */
class Stats
{

	public const POST_META_KEY = 'cf_speed_daemon_stats';

	/**
	 * Object id (post id, term id).
	 *
	 * @var int
	 */
	protected $objectId;

	/**
	 * Array with original css fragments.
	 *
	 * @var array
	 */
	protected $before;

	/**
	 * Array with optimised css.
	 *
	 * @var string
	 */
	protected $after;

	/**
	 * Stats constructor.
	 *
	 * @param int $objectId Object id (post id).
	 */
	public function __construct($objectId)
	{
		$this->objectId = $objectId;
	}

	/**
	 * Calculate CSS before and after sizes.
	 *
	 * @return array
	 */
	protected function calculateSizes()
	{
		$beforeTotal = 0;
		$afterTotal  = 0;

		foreach ($this->before as $css) {
			$beforeTotal += strlen($css);
		}

		$afterTotal += strlen($this->after);

		return [
			'before_size'       => $beforeTotal / 1024,
			'after_size'        => $afterTotal / 1024,
			'reduction_percent' => ( 1 - $afterTotal / $beforeTotal ) * 100,
		];
	}

	/**
	 * Saves data stats to post meta.
	 *
	 * @param array  $beforeStyles Array with css fragments.
	 * @param string $afterStyle   Optimised css fragment.
	 *
	 * @return bool True if successful,/
	 */
	public function save($beforeStyles, $afterStyle)
	{
		$this->before = $beforeStyles;
		$this->after  = $afterStyle;

		return update_post_meta($this->objectId, self::POST_META_KEY, $this->calculateSizes());
	}

	/**
	 * Load stats from post meta.
	 *
	 * @return array
	 */
	public function load()
	{
		return get_post_meta($this->objectId, self::POST_META_KEY, true);
	}
}
