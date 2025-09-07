<?php

/**
 * Manages the filesystem.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use WP_Error;
use WP_Filesystem_Base;

/**
 * The Filesystem Class
 */
class Filesystem
{

	/**
	 * Base dir for files.
	 *
	 * @var string
	 */
	public $baseDir;

	/**
	 * Filesystem has the right permisions.
	 *
	 * @access private
	 * @var bool
	 */
	public $fs_status = false;

	/**
	 * WP_Filesystem API.
	 *
	 * @access private
	 * @var bool
	 */
	private $wpFileApi = false;

	/**
	 * Filesystem singleton instance.
	 *
	 * @access private
	 * @static
	 * @var Core
	 */
	private static $instance;

	/**
	 * Get Filesystem singleton instance.
	 *
	 * @access public
	 * @static
	 * @return Filesystem
	 */
	public static function getInstance()
	{
		if (! self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Filesystem constructor.
	 *
	 * Initiate file system for read/write operations.
	 *
	 * @access private
	 */
	private function __construct()
	{
		$this->fs_status = $this->checkFileSystem();

		if (! defined('WP_CONTENT_DIR')) {
			define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
		}

		$this->baseDir = WP_CONTENT_DIR . '/speed-daemon-css/';
	}


	/**
	 * Initiate the wp filesystem.
	 *
	 * @access private
	 * @return bool|WP_Error  Return true if everything is ok.
	 */
	private function checkFileSystem()
	{
		// Need to include file.php for frontend.
		if (! function_exists('request_filesystem_credentials')) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		/**
		 * Removes CRITICAL Uncaught Error:
		 * Call to undefined function submit_button() in wp-admin/includes/file.php:1287.
		 */
		require_once ABSPATH . 'wp-admin/includes/template.php';

		// Check if the user has write permissions.
		$accessType = get_filesystem_method();
		if ('direct' === $accessType) {
			$this->wpFileApi = true;

			// You can safely run request_filesystem_credentials() without any issues
			// and don't need to worry about passing in a URL.
			$credentials = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, null);

			// Initialize the Filesystem API.
			if (! WP_Filesystem($credentials)) {
				// Some problems, exit.
				return new WP_Error(
					'fs-error',
					// phpcs:ignore Generic.Files.LineLength.TooLong
					__('Error: Unexpected error while writing a file. Please view error log for more information.', 'cf-speed-daemon')
				);
			}
		} else {
			// Don't have direct write access.
			$this->wpFileApi = false;
		}

		// Can not write to wp-content directory.
		if (defined(WP_CONTENT_DIR) && ! is_writeable(WP_CONTENT_DIR)) {
			return new WP_Error(
				'fs-error',
				// phpcs:ignore Generic.Files.LineLength.TooLong
				__('Error: The wp-content directory is not writable. Ensure the folder has read/write permissions to function successfully.', 'cf-speed-daemon')
			);
		}

		return true;
	}

	/**
	 * Check if the cache folder exists. If not, create it.
	 *
	 * @return WP_Error
	 */
	private function checkBaseDir()
	{
		// Check if cache folder exists. If not, create it.
		if (! is_dir($this->baseDir)) {
			if (! @wp_mkdir_p($this->baseDir)) {
				return new WP_Error(
					'fs-dir-error',
					sprintf(
						/* translators: %s: directory */
						__('Error creating directory %s.', 'cf-speed-daemon'),
						esc_html($this->baseDir)
					)
				);
			}
		}
	}

	/**
	 * Maybe create the time subdir folders.
	 *
	 * @param string $subdir The subdir path.
	 * @return WP_Error
	 */
	private function maybeCreateTimeSubdir($subdir)
	{
		if (empty($subdir)) {
			return;
		}

		// Check if cache folder exists. If not, create it.
		if (! is_dir($this->baseDir . $subdir)) {
			if (! @wp_mkdir_p($this->baseDir . $subdir)) {
				return new WP_Error(
					'fs-dir-error',
					sprintf(
						/* translators: %s: directory */
						__('Error creating subdirectory %s.', 'cf-speed-daemon'),
						esc_html($this->baseDir)
					)
				);
			}
		}
	}

	/**
	 * Write file to baseDir.
	 *
	 * @param  string $file      The name of the file.
	 * @param  string $content   The file contents.
	 * @param  string $time      The object time in 'Y-m-d H:i:s' format.
	 * @return bool|WP_Error
	 */
	public function write($file, $content = '', $time = null)
	{
		if (is_wp_error($this->fs_status)) {
			return false;
		}
		$this->checkBaseDir();

		// Remove directory from file.
		$file = basename($file);

		// Maybe create time subdir.
		$subdir = '';
		if (
			! empty($time)
			&& \DateTime::createFromFormat('Y-m-d H:i:s', $time)
		) {
			// Generate the yearly and monthly dirs.
			$y      = substr($time, 0, 4);
			$m      = substr($time, 5, 2);
			$subdir = "$y/$m/";
			$file   = $subdir . $file;
			$this->maybeCreateTimeSubdir($subdir);
		}

		// Use WP_Filesystem API.
		if ($this->wpFileApi) {
			/**
			 * WP_Filesystem global.
			 *
			 * @var WP_Filesystem_Base $wp_filesystem
			 */
			global $wp_filesystem;

			// Create the file.
			if (! $wp_filesystem->put_contents($this->baseDir . $file, $content, FS_CHMOD_FILE)) {
				return new WP_Error(
					'fs-file-error',
					sprintf(
						/* translators: %s: file */
						__('Error uploading file %s.', 'cf-speed-daemon'),
						esc_html($file)
					)
				);
			}
		} else {
			// Create the file.
			$file = fopen($this->baseDir . $file, 'w');
			if (! fwrite($file, $content)) {
				return new WP_Error(
					'fs-file-error',
					sprintf(
						/* translators: %s: file */
						__('Error uploading file %s.', 'cf-speed-daemon'),
						esc_html($file)
					)
				);
			} elseif ($file) {
				fclose($file);
			}
		}

		return true;
	}

	/**
	 * Remove the css folder on uninstall.
	 *
	 * @return bool
	 */
	public function cleanUp()
	{
		if (is_wp_error($this->fs_status)) {
			return false;
		}

		// Use WP_Filesystem API.
		if ($this->wpFileApi) {
			/**
			 * WP_Filesystem global.
			 *
			 * @var WP_Filesystem_Base $wp_filesystem
			 */
			global $wp_filesystem;

			if (! $wp_filesystem->delete($this->baseDir, true)) {
				return false;
			}
		} else {
			// Use direct filesystem php functions.
			if (! $this->nativeDirDelete($this->baseDir)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Native php directory removal (used when WP_Filesystem is not available);
	 *
	 * @access private
	 * @param string $path  The Path to be deleted.
	 * @return bool
	 */
	private function nativeDirDelete($path)
	{
		if (is_wp_error($this->fs_status)) {
			return false;
		}

		// Use direct filesystem php functions.
		$dir = @opendir($path);

		while (false !== ( $file = readdir($dir) )) {
			if ('.' === $file || '..' === $file) {
				continue;
			}

			$full = $path . '/' . $file;
			if (is_dir($full)) {
				$this->nativeDirDelete($full);
			} else {
				@unlink($full);
			}
		}

		closedir($dir);
		@rmdir($path);

		return true;
	}

	/**
	 * Check if file exists.
	 *
	 * @param  string $file  File to be find.
	 * @return bool
	 */
	public function exists($file)
	{
		if (is_wp_error($this->fs_status)) {
			return false;
		}

		$path = $this->baseDir;

		// Maybe use WP_Filesystem API.
		if ($this->wpFileApi) {
			/**
			 * WP_Filesystem global.
			 *
			 * @var WP_Filesystem_Base $wp_filesystem
			 */
			global $wp_filesystem;
			return $wp_filesystem->exists($path . $file);
		} else {
			// Use direct filesystem php functions.
			return file_exists($path . $file);
		}
	}

	/**
	 * Get the file contents of a file.
	 *
	 * @param  string $file The file path.
	 * @return bool
	 */
	public function getContents($file)
	{
		if (is_wp_error($this->fs_status)) {
			return false;
		}

		// Our folder path.
		$path = $this->baseDir;
		// Remove directory from file.
		$file = basename($file);

		// Maybe use WP_Filesystem API.
		if ($this->wpFileApi) {
			/**
			 * WP_Filesystem global.
			 *
			 * @var WP_Filesystem_Base $wp_filesystem
			 */
			global $wp_filesystem;
			return $wp_filesystem->get_contents($path . $file);
		} else {
			// Use direct filesystem php functions.
			return @file_get_contents($path . $file);
		}
	}

	/**
	 * Log description and error.
	 *
	 * @param string $description Description.
	 * @param mixed $error Error.
	 *
	 * @return bool
	 */
	public function log($description, $error)
	{
		$content = sprintf('%1$s - %2$s', date('Y-m-d H:i:s'), $description) . PHP_EOL;

		if (is_wp_error($error)) {
			$content .= $error->get_error_message();
		} elseif (is_array($error) && !empty($error['body'])) {
			$content .= $error['body'];
		} else {
			$content .= var_export($error, true);
		}

		$content .= PHP_EOL;

		return error_log(
			$content,
			3,
			$this->baseDir . CF_SPEED_DAEMON_DEBUG_LOG_FILE
		);
	}
}
