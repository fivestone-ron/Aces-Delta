<?php

/**
 * The plugin first class.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF;

/**
 * The main class
 */
class Plugin
{

	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Main
	 */
	private static $instance;

	/**
	 * Core main class
	 *
	 * @var Core\Core
	 */
	public $core;

	/**
	 * Admin main class
	 *
	 * @var Admin\Admin
	 */
	public $admin;

	/**
	 * Addons main class
	 *
	 * @var Addons\Addons
	 */
	public $addons;

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
	 * The main class construct.
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 */
	private function init()
	{
		// Initialize the plugin Core.
		$this->core = Core\Core::getInstance();
		// Initialize the plugin Addons.
		$this->addons = Addons\Addons::getInstance();

		if (is_user_logged_in()) {
			// Initialize admin core files.
			$this->admin = Admin\Admin::getInstance();
		}

		register_activation_hook(
			CF_SPEED_DAEMON_FILE,
			['CrowdFavorite\SpeedDaemonCF\Core\\Installer', 'activate']
		);
		register_deactivation_hook(
			CF_SPEED_DAEMON_FILE,
			['CrowdFavorite\SpeedDaemonCF\Core\\Installer', 'deactivate']
		);

		// Triggered when Speed Daemon by Crowd Favorite is totally loaded.
		do_action('cf_speed_daemon_loaded');
	}
}
