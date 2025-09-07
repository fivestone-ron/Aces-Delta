<?php

/**
 * Class responsible with dequeue all styles and enqueue new purged css file.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use CrowdFavorite\SpeedDaemonCF\Admin\Settings;

/**
 * Style Manager class.
 */
class StyleManager
{

	/**
	 * The handles to be ignored from removing.
	 *
	 * @var array
	 */
	public $ignoreHandles;

	/**
	 * StyleManager constructor.
	 */
	public function __construct()
	{
		// On the backend or is a Purge is on going, we do nothing.
		if (! is_admin() && ! CF_SPEED_DAEMON_IS_PURGING && ! CF_SPEED_DAEMON_IS_DISABLED) {
			// Add the initial logic.
			add_action('init', [$this, 'init']);
		}
	}

	/**
	 * The init hook.
	 *
	 * @return void
	 */
	public function init()
	{
		$this->ignoreHandles = apply_filters(
			'cf_speed_daemon_ignore_handles',
			array_merge(
				['dashicons', 'admin-bar'],
				$this->getExcludedHandles()
			)
		);

		// Register our style and make sure to load it on header.
		add_action('wp_enqueue_scripts', [$this, 'registerOptimalStyle']);
		// Check the list.
		add_filter('print_styles_array', [$this, 'replaceStyles'], PHP_INT_MAX);
	}

	/**
	 * Replace the page styling with the optimized one.
	 *
	 */
	public function registerOptimalStyle()
	{
		$fileName   = $this->getCurrentUrlFilename();
		$filesystem = Filesystem::getInstance();

		if (
			empty($fileName)
			|| ! $filesystem->exists($fileName)
			|| apply_filters('cf_speed_daemon_stop_replace_styles', false)
		) {
			return false;
		}

		wp_enqueue_style(
			CF_SPEED_DAEMON_PLUGIN_HANDLE,
			get_site_url() . '/wp-content/speed-daemon-css/' . $fileName,
			[],
			filemtime($filesystem->baseDir . $fileName)
		);
	}

	/**
	 * Replace the page styling with the optimized one.
	 *
	 * @param  string[] $to_do The list of enqueued style handles about to be processed.
	 * @return string[] The optimzied css only.
	 */
	public function replaceStyles($to_do)
	{
		$fileName   = $this->getCurrentUrlFilename();
		$filesystem = Filesystem::getInstance();

		if (
			empty($fileName)
			|| ! $filesystem->exists($fileName)
			|| apply_filters('cf_speed_daemon_stop_replace_styles', false)
		) {
			return $to_do;
		}

		// We only load the optimized css in the header.
		if (doing_action('wp_head')) {
			return [CF_SPEED_DAEMON_PLUGIN_HANDLE];
		}

		if (! empty($to_do)) {
			foreach ($to_do as $key => $styleHandle) {
				// If the handle is ignored, continue.
				if (in_array($styleHandle, $this->ignoreHandles, true)) {
					continue;
				}

				// If the handle is admin related, like top bars customizations, continue.
				if ($this->isAdminStyle($styleHandle)) {
					continue;
				}

				wp_deregister_style($styleHandle);
				wp_dequeue_style($styleHandle);
				unset($to_do[$key]);
			}
		}
		return $to_do;
	}

	/**
	 * Get current file id.
	 *
	 * @return int
	 */
	protected function getCurrentUrlFilename()
	{
		$object     = get_queried_object();
		$filesystem = Filesystem::getInstance();
		$subdir     = '';

		if (empty($object)) {
			return false;
		}

		if (get_class($object) === 'WP_Post') {
			$ignore    = get_post_meta($object->ID, 'cf_speed_daemon_ignore', true);
			$time      = get_post_time('Y-m-d H:i:s', true, $object->ID);
			$subdir    = $this->maybeGetTimeSubdir($time);
			$fileName = apply_filters(
				'cf_speed_daemon_filename',
				'post-' . $object->ID,
				$object
			);
			if (! empty($ignore)) {
				return false;
			}
		} elseif (get_class($object) === 'WP_Term') {
			$fileName = apply_filters(
				'cf_speed_daemon_filename',
				'term-' . $object->term_id,
				$object
			);
		} elseif (get_class($object) === 'WP_User') {
			$fileName = apply_filters('cf_speed_daemon_filename', 'author-' . $object->ID, $object);
		}

		return $subdir . $fileName . '.css';
	}

	/**
	 * Maybe return a time subdir.
	 *
	 * @param  string $time      The object time in 'Y-m-d H:i:s' format.
	 * @return string
	 */
	protected function maybeGetTimeSubdir($time)
	{
		$subdir = '';
		if (
			! empty($time)
			&& \DateTime::createFromFormat('Y-m-d H:i:s', $time)
		) {
			// Generate the yearly and monthly path.
			$y      = substr($time, 0, 4);
			$m      = substr($time, 5, 2);
			$subdir = "$y/$m/";
		}
		return $subdir;
	}

	/**
	 * Check if style handle is for admin pages.
	 *
	 * @param string $styleHandle Style handle.
	 *
	 * @return bool
	 */
	protected function isAdminStyle($styleHandle)
	{
		// @TODO: check using a more reliable way than this.
		return stripos($styleHandle, 'admin') !== false;
	}

	/**
	 * Get list of excluded handles and remove their prefix `-css` if present.
	 *
	 * @return array
	 */
	protected function getExcludedHandles()
	{
		$handles = Settings::getListValuesFromOption('exclude_handles');
		return array_map(function ($handle) {
			return str_replace('-css', '', $handle);
		}, $handles);
	}
}
