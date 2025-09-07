<?php

/**
 * Manages the plugin activation/deactivation and initializes the container.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use CrowdFavorite\SpeedDaemonCF\Admin\Settings;

/**
 * The main class
 */
class Cron
{

	/**
	 * The cron hook key.
	 *
	 * @access private
	 * @static
	 *
	 * @var Core
	 */
	protected $cronHookKey = 'cf_speed_daemon_cron';

	/**
	 * The cron interval.
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $cronInterval = 'cf_speed_daemon_cron_interval';

	/**
	 * Initiate the background process.
	 */
	public function __construct()
	{
		$use_cron = Settings::getOptionValue('use_cron');
		if (! empty($use_cron)) {
			$this->maybeStartCron();
		}

		add_action(
			$this->cronHookKey,
			[$this, 'process']
		);

		add_action(
			'cf_speed_daemon_deactivated',
			[$this, 'stopCron']
		);
	}

	/**
	 * Process the cron.
	 */
	public function process()
	{
		// Process only the requests not made by the plugin to avoid loops.
		if (! CF_SPEED_DAEMON_IS_PURGING) {
			$this->processPostTypes();
			// $this->processTaxonomies();
			// $this->processBlogIndex();
		}
	}

	/**
	 * Process the wp blog index page.
	 *
	 * @return boolean False if the front is a static page.
	 */
	private function processBlogIndex()
	{
		$show_on_front = get_option('show_on_front');

		if ('page' === $show_on_front) {
			return false;
		}
		$home_url = get_home_url();
		Core::getInstance()->purger()->purgeUrl($home_url);
	}

	/**
	 * Process the wp posts table.
	 */
	private function processPostTypes()
	{
		$args = apply_filters(
			'cf_speed_daemon_post_args',
			[
				'posts_per_page' => 10,
				'post_type'      => [
					'post',
					'page',
				],
				'meta_query'     => [
					[
						'key'     => Purger::PURGER_META_KEY,
						'compare' => 'NOT EXISTS',
					],
				],
			]
		);

		$query = new \WP_Query($args);

		// If we have posts un optimized.
		if ($query->have_posts()) {
			// Get them as array.
			$posts = $query->get_posts();
			if (! empty($posts) && is_array($posts)) {
				foreach ($posts as $post) {
					Core::getInstance()->purger()->purgePost($post);
				}
			}
		}
	}

	/**
	 * Process the taxonomies terms.
	 *
	 * @return void
	 */
	private function processTaxonomies()
	{
		$args = ['public' => true];

		$taxonomies = get_taxonomies($args);

		if (! empty($taxonomies) && ! is_wp_error($taxonomies)) {
			foreach ($taxonomies as $taxonomy) {
				$terms = get_terms(
					[
						'taxonomy'   => $taxonomy,
						'number'     => 10,
						'meta_query' => [
							[
								'key'     => Purger::PURGER_META_KEY,
								'compare' => 'NOT EXISTS',
							],
						],
					]
				);
				if (! empty($terms) && ! is_wp_error($terms)) {
					foreach ($terms as $term) {
						Core::getInstance()->purger()->processTerm($term);
					}
				}
			}
		}
	}

	/**
	 * Start the cron.
	 */
	public function maybeStartCron()
	{
		if (! wp_next_scheduled($this->cronHookKey)) {
			wp_schedule_event(time(), 'hourly', $this->cronHookKey);
		}
	}

	/**
	 * Stop the cron.
	 */
	public function stopCron()
	{
		wp_clear_scheduled_hook($this->cronHookKey);
	}
}
