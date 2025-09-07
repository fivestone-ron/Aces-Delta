<?php //phpcs:disable Files.SideEffects.FoundWithSymbols

/**
 * Plugin Name: Speed Daemon by Crowd Favorite
 * Description: Optimizes the CSS necessary for each page of a WordPress site.
 * Plugin URI: https://www.crowdfavorite.com
 * Author: Crowd Favorite
 * Author URI: https://www.crowdfavorite.com
 * Version: 1.0.0
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Constant Definitions.
define('CF_SPEED_DAEMON_FILE', __FILE__);
define('CF_SPEED_DAEMON_DIR', plugin_dir_path(CF_SPEED_DAEMON_FILE));
define('CF_SPEED_DAEMON_PLUGIN_BASENAME', plugin_basename(CF_SPEED_DAEMON_FILE));
define('CF_SPEED_DAEMON_URL', plugin_dir_url(CF_SPEED_DAEMON_FILE));

// Always mention the plugin version.
define('CF_SPEED_DAEMON_PLUGIN_VER', '1.0.0');

// Define the plugin assets handle, this is used to give unique names to the plugin scripts.
define('CF_SPEED_DAEMON_PLUGIN_HANDLE', 'cf-speed-daemon');

// Define the plugin localized handle.
define('CF_SPEED_DAEMON_PLUGIN_LOCALIZED_OBJECT_NAME', 'CFSpeedDaemonCF');

// Define REST API base url.
define('CF_SPEED_DAEMON_REST_API_BASE_URL', 'https://cf-critical-css.favoriteers.com/api');

// Define SHOP website url.
define('CF_SPEED_DAEMON_SHOP_SITE_URL', 'https://shop.crowdfavorite.com');

// If it's a purge.
$isPurging = filter_input(INPUT_GET, 'cf-speed-daemon');
define('CF_SPEED_DAEMON_IS_PURGING', $isPurging !== null);

$isDisabled = filter_input(INPUT_GET, 'cf-speed-daemon-disable');
define('CF_SPEED_DAEMON_IS_DISABLED', $isDisabled !== null);

// Define debug log file name.
define('CF_SPEED_DAEMON_DEBUG_LOG_FILE', 'debug.log');

// Only require the autoload.php file if it exists.
// If it does not, assume that it is the root project's responsibility to load the necessary files.
call_user_func_array(function ($absPath, $rootPath) {

	$autoload = "{$rootPath}vendor/autoload.php";
	if (is_readable($autoload)) {
		require_once $autoload;
	}
	// Init the plugin and load the plugin instance for the first time.
	add_action('plugins_loaded', ['CrowdFavorite\SpeedDaemonCF\\Plugin', 'getInstance']);
}, [ABSPATH, CF_SPEED_DAEMON_DIR]);
