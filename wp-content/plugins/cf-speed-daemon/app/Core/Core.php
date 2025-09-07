<?php

/**
 * Manages the plugin activation/deactivation and initializes the container.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use CrowdFavorite\SpeedDaemonCF\Admin\Settings;

/**
 * The main core.
 */
class Core
{

	/**
	 * Core singleton instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Core
	 */
	private static $instance;

	/**
	 * Style manager instance.
	 *
	 * @var StyleManager
	 */
	private $styleManager;

	/**
	 * Cron manager instance.
	 *
	 * @var Cron
	 */
	private $cron;

	/**
	 * Filesystem instance.
	 *
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Purger instance.
	 *
	 * @var Purger
	 */
	private $purger;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return Main
	 */
	public static function getInstance()
	{
		if (! self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the Cron instance.
	 *
	 * @return Cron
	 */
	public function cron()
	{
		return $this->cron;
	}

	/**
	 * Get the Filesystem instance.
	 *
	 * @return Filesystem
	 */
	public function filesystem()
	{
		return $this->filesystem;
	}

	/**
	 * Get the Purger instance.
	 *
	 * @return Purger
	 */
	public function purger()
	{
		return $this->purger;
	}

	/**
	 * The main class construct.
	 */
	public function __construct()
	{
		$disabled = Settings::getOptionValue('disable_css_optimisations') || !LicenseManager::isActivated();

		if ($disabled) {
			return;
		}

		$this->styleManager  = new StyleManager();
		$this->cron          = new Cron();
		$this->purger        = new Purger();
		$this->filesystem    = Filesystem::getInstance();

		// The init hook.
		add_action('init', [$this, 'init']);

		// Template redirect hook.
		add_action('template_redirect', [$this, 'refreshOptimization']);
		add_action('admin_init', [$this, 'refreshOptimization']);
	}

	/**
	 * Logic that need to be done in the init hook.
	 *
	 * @return void
	 */
	public function init()
	{
		// Remove the redirect to pretty urls when getting the page content.
		if (CF_SPEED_DAEMON_IS_PURGING) {
			remove_filter('template_redirect', 'redirect_canonical');
		}

		// Associate action with purge method.
		add_action('purgePostOnSave', [$this, 'purgePostOnSave'], 10, 2);
		add_action('save_post', [$this, 'maybePurgePostOnSave'], 10, 2);
	}

	/**
	 * Remove all the posts meta created by the plugin on deactivate.
	 *
	 * @return void
	 */
	public function removeAllPostMeta()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		$wpdb->delete($table, ['meta_key' => Purger::PURGER_META_KEY]);
	}

	/**
	 * Remove all the posts meta created by the plugin on deactivate.
	 *
	 * @return void
	 */
	public function removeAllTermMeta()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'termmeta';
		$wpdb->delete($table, ['meta_key' => Purger::PURGER_META_KEY]);
	}

	/**
	 * Template redirect.
	 */
	public function refreshOptimization()
	{
		if ($post_id = filter_input(INPUT_GET, 'cf-speed-daemon-refresh')) {
			$post = get_post($post_id);
			// Reprocess page.
			$this->purger()->purgePost($post);

			// Redirect back to original url.
			$url = add_query_arg(['cf-speed-daemon-refresh' => null]);
			wp_safe_redirect($url);
			die();
		}
	}

	/**
	 * Purge post on save only if there's a hook associated with the following action.
	 *
	 * @param int      $post_ID Post ID.
	 * @param \WP_Post $post    Post object.
	 */
	public function maybePurgePostOnSave($post_ID, $post)
	{
		do_action('purgePostOnSave', $post_ID, $post);
	}

	/**
	 * The save_post hook  callback.
	 *
	 * @param  int     $post_ID The post ID.
	 * @param  WP_Post $post    The post object.
	 * @return void
	 */
	public function purgePostOnSave($post_ID, $post)
	{
		$save_event = Settings::getOptionValue('save_event');
		if ($save_event) {
			$this->purger()->purgePost($post);
		}
	}
}
