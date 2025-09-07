<?php

/**
 * Manages activation/deactivation and upgrades
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

/**
 * The Installer class
 */
class Installer
{

	/**
	 * Do on plugin activate.
	 */
	public static function activate()
	{
		// After we finished the activation.
		do_action('cf_speed_daemon_activatd');
	}

	/**
	 * Do on plugin deactivation.
	 */
	public static function deactivate()
	{
		// After we finished the deactivation.
		do_action('cf_speed_daemon_deactivated');

		$core = Core::getInstance();
		$fs   = Filesystem::getInstance();

		// Remove the generated css folder.
		if (! is_wp_error($fs->fs_status)) {
			$fs->cleanUp();
		}

		// Remove all the meta created by the plugin.
		$core->removeAllPostMeta();
		$core->removeAllTermMeta();

		delete_option('cf_speed_daemon_license');
	}
}
