<?php

/**
 * Manages the plugin activation/deactivation and initializes the container.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Admin;

use CrowdFavorite\SpeedDaemonCF\Core\Core;
use CrowdFavorite\SpeedDaemonCF\Core\LicenseManager;
use CrowdFavorite\SpeedDaemonCF\Core\Stats;
use CrowdFavorite\SpeedDaemonCF\Core\Purger;
use WP_Admin_Bar;
use WP_Query;

/**
 * The main class
 */
class Admin
{

	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Admin
	 */
	private static $instance;

	/**
	 * Settings manager instance.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return Admin
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
		$this->settings = new Settings(); //@TODO: use DI instead of instancing a class as a class property
		add_action(
			'admin_init',
			[$this->settings, 'adminInit'],
			100
		);

		// Register settings page.
		add_action(
			'admin_menu',
			[$this->settings, 'registerSettingsPage']
		);

		add_action(
			'admin_enqueue_scripts',
			[$this, 'enqueueScripts']
		);

		add_action(
			'admin_bar_menu',
			[$this, 'extendAdminBar'],
			100
		);

		add_action('manage_pages_custom_column', [$this, 'handleCustomColumn'], 10, 2);
		add_filter('manage_pages_columns', [$this, 'setCustomColumns']);
		add_action('manage_posts_custom_column', [$this, 'handleCustomColumn'], 10, 2);
		add_filter('manage_posts_columns', [$this, 'setCustomColumns']);
		add_filter('plugin_action_links_' . CF_SPEED_DAEMON_PLUGIN_BASENAME, [$this, 'addPluginLinks']);
		add_action('add_meta_boxes', [$this, 'addPostMetaBoxSettings']);
		add_action('save_post', [$this, 'savePostMetaBoxes'], 10, 2);
		add_action('admin_notices', [$this, 'showGeneralAdminNotice']);
		add_filter('page_row_actions', [$this, 'pageTableQuickActions'], 99, 2);
		add_filter('post_row_actions', [$this, 'pageTableQuickActions'], 99, 2);

		// Bulk actions.
		add_filter('bulk_actions-edit-page', [$this, 'registerBulkActions']);
		add_filter('bulk_actions-edit-post', [$this, 'registerBulkActions']);
		add_filter('handle_bulk_actions-edit-page', [$this, 'handleBulkActions'], 10, 3);
		add_filter('handle_bulk_actions-edit-post', [$this, 'handleBulkActions'], 10, 3);
		// Bulk messages.
		add_action('admin_notices', [$this, 'bulkActionsMessages']);
	}

	/**
	 * Add the bulk optimization for pages.
	 *
	 * @param  array $bulkActions The bulk option.
	 * @return array
	 */
	public function registerBulkActions($bulkActions)
	{
		$bulkActions['speedDaemonOptimize'] = __('Optimize the CSS', 'cf-speed-daemon');
		return $bulkActions;
	}

	/**
	 * Handle the bulk optimization for pages.
	 *
	 * @param  string $redirectTo The redirect to
	 * @param  string $doAction   The action name.
	 * @param  array  $postIds    The selected posts.
	 * @return string The redirect url.
	 */
	public function handleBulkActions($redirect, $doAction, $postIDs)
	{
		$redirect = remove_query_arg('speedDaemonOptimizeDone', $redirect);

		if ($doAction !== 'speedDaemonOptimize') {
			return $redirect;
		}

		/*
		 * The posts as objects.
		 */
		$args = array(
			'ignore_sticky_posts' => 1,
			'post__in' => $postIDs,
			'post_type' => ['page', 'post'],
		);
		$query = new WP_Query($args);

		// If we have posts to be optimized.
		if ($query->have_posts()) {
			// Get them as array.
			$posts = $query->get_posts();
			$postsCount = 0;

			if (!empty($posts) && is_array($posts)) {
				foreach ($posts as $post) {
					Core::getInstance()->purger()->purgePost($post);
					$postsCount = $postsCount + 1;
				}
				$redirect = add_query_arg(
					'speedDaemonOptimizeDone',
					$postsCount, // Parameter value - how many pages have been affected.
					$redirect
				);
			}
		}
		return $redirect;
	}

	/**
	 * Bulk messages for bulk optimizations.
	 *
	 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type.
	 * @param  array $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 * @return array The new messages.
	 */
	public function bulkActionsMessages()
	{
		$speedDaemonOptimizeDone = filter_input(INPUT_GET, 'speedDaemonOptimizeDone');
		if (!empty($speedDaemonOptimizeDone)) {
			echo '<div class="updated notice is-dismissible">';
			echo '<p>' . wp_kses_post(
				sprintf(
					_n(
						'%d page was optimized.',
						'%d pages were optimized.',
						(int) $speedDaemonOptimizeDone,
						'cf-speed-daemon'
					),
					(int) $speedDaemonOptimizeDone,
				)
			) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * `wp_enqueue_scripts` hook.
	 *
	 * @return void
	 */
	public function enqueueScripts()
	{
		wp_enqueue_script(
			CF_SPEED_DAEMON_PLUGIN_HANDLE,
			CF_SPEED_DAEMON_URL . 'build/app.js',
			[],
			filemtime(CF_SPEED_DAEMON_DIR . 'build/app.js'),
			true
		);

		wp_enqueue_style(
			CF_SPEED_DAEMON_PLUGIN_HANDLE,
			CF_SPEED_DAEMON_URL . 'build/app.css',
			[],
			filemtime(CF_SPEED_DAEMON_DIR . 'build/app.css')
		);
	}

	/**
	 * Add Settings and Help links to plugin page.
	 *
	 * @param array $links Links to be outputted
	 *
	 * @return array $links Updated links to be outputted
	 */
	public function addPluginLinks($links)
	{
		$help_link = '<a href="'
			. esc_url(admin_url('admin.php?page=cf-speed-daemon-help')) . '">'
			. esc_html__('Help', 'cf-speed-daemon')
			. '</a>';
		$settings_link = '<a href="'
			. esc_url(admin_url('admin.php?page=cf-speed-daemon')) . '">'
			. esc_html__('Settings', 'cf-speed-daemon')
			. '</a>';
		$links[] = $settings_link;
		$links[] = $help_link;
		return $links;
	}

	/**
	 * Add item notification to admin bar.
	 *
	 * @param WP_Admin_Bar $admin_bar Admin Bar.
	 */
	public function extendAdminBar($admin_bar)
	{
		if (is_admin() || CF_SPEED_DAEMON_IS_DISABLED) {
			return;
		}

		global $post;
		if (!$post) {
			return;
		}

		$disabled = $this->settings->getOptionValue('disable_css_optimisations') || !LicenseManager::isActivated();
		if ($disabled) {
			return;
		}

		$stats   = new Stats($post->ID); //@TODO: use DI instead of instancing a class inside a class method
		$report  = $stats->load();

		$purger = new Purger();
		$errors = $purger->getErrors($post->ID);
		if ($errors) {
			$title = esc_html(
				sprintf(
					__('Optimize page CSS (%s)', 'cf-speed-daemon'),
					substr($errors, 0, 30) . '...'
				)
			);
		} elseif (empty($report)) {
			$title = esc_html__('Optimize page CSS', 'cf-speed-daemon');
		} else {
			$title = sprintf(
				/* Translators: %1$d - reduction percent;
				%2$d - css size before optimisation; %3$d - css size after optimisation */
				esc_html__('Page CSS reduced by %1$d%% (from %2$d kB to %3$d kB)', 'cf-speed-daemon'),
				$report['reduction_percent'],
				$report['before_size'],
				$report['after_size']
			);
		}

		$href = add_query_arg(['cf-speed-daemon-refresh' => $post->ID]);
		$queryArgsToRemove = apply_filters('cf_speed_daemon_remove_url_args', []);

		if (!empty($queryArgsToRemove)) {
			$href = remove_query_arg($queryArgsToRemove, $href);
		}

		$admin_bar->add_node([
			'id'    => 'cf-speed-daemon-admin-bar',
			'title' => $title,
			'href'  => $href,
			'meta'  => ['title' => esc_html__('Speed Daemon by CrowdFavorite', 'cf-speed-daemon')]
		]);

		if ($report) {
			$admin_bar->add_group([
				'id'     => 'cf-speed-daemon-group',
				'parent' => 'cf-speed-daemon-admin-bar',
			]);

			$admin_bar->add_node([
				'id'     => 'cf-speed-daemon-disable-admin-bar',
				'title'  => esc_html__('View without CSS optimizations', 'cf-speed-daemon'),
				'href'   => add_query_arg(['cf-speed-daemon-disable' => 1]),
				'meta'   => ['target' => '_blank'],
				'parent' => 'cf-speed-daemon-group',
			]);
		}
	}

	/**
	 * Set custom columns for post and pages post types.
	 *
	 * @param array $columns Post and pages admin columns.
	 *
	 * @return array
	 */
	public function setCustomColumns($columns)
	{
		if (CF_SPEED_DAEMON_IS_DISABLED) {
			return $columns;
		}

		$disabled = $this->settings->getOptionValue('disable_css_optimisations') || !LicenseManager::isActivated();
		if ($disabled) {
			return $columns;
		}

		$columns['cf_speed_daemon_refresh'] = esc_html__('Speed Daemon', 'cf-speed-daemon');
		$columns['cf_speed_daemon_stats'] = esc_html__('Optimized CSS stats', 'cf-speed-daemon');

		return $columns;
	}

	/**
	 * Add data to custom columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 */
	public function handleCustomColumn($column, $post_id)
	{
		if ('cf_speed_daemon_stats' === $column) {
			$this->handleStatsColumn($column, $post_id);
		} elseif ('cf_speed_daemon_refresh' === $column) {
			$this->handleRefreshColumn($column, $post_id);
		}
	}

	/**
	 * Handle stats column.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 */
	private function handleStatsColumn($column, $post_id)
	{
		$stats  = new Stats($post_id); //@todo: use di instead of instancing a class in a method!
		$report = $stats->load();

		if (empty($report)) {
			return;
		}

		echo esc_html(
			sprintf(
				/* Translators: %1$d - reduction percent;
				%2$d - css size before optimization; %3$d - css size after optimisation */
				__('%1$d%% optimization (from %2$d kB to %3$d kB)', 'cf-speed-daemon'),
				$report['reduction_percent'],
				$report['before_size'],
				$report['after_size']
			)
		);
	}

	/**
	 * Handle refresh column.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 */
	private function handleRefreshColumn($column, $post_id)
	{
		if (CF_SPEED_DAEMON_IS_DISABLED) {
			return;
		}

		$disabled = $this->settings->getOptionValue('disable_css_optimisations') || !LicenseManager::isActivated();
		if ($disabled) {
			return;
		}

		$href = add_query_arg(['cf-speed-daemon-refresh' => $post_id]);
		$queryArgsToRemove = apply_filters('cf_speed_daemon_remove_url_args', []);
		$img = '<img height="20px" src="' . esc_url(CF_SPEED_DAEMON_URL . '/icon.png') . '" />';
		echo wp_kses_post(
			'<a title="' . esc_html__('Optimize page', 'cf-speed-daemon') . '" href="' . $href . '"> ' . $img . '</a>'
		);
	}

	/**
	 * Add the page/post ignore setting.
	 */
	public function addPostMetaBoxSettings()
	{
		add_meta_box(
			'cf-speed-daemon-meta-box',
			esc_html__('Speed Daemon by CrowdFavorite', 'cf-speed-daemon'),
			[$this, 'displayCheckbox'],
			['post', 'page'],
			'side',
			'low',
			['name' => 'ignore_post']
		);
	}

	/**
	 * Add markup for page checkbox.
	 *
	 * @param array $post WP_Post.
	 */
	public function displayCheckbox($post)
	{
		$ignoreThis = get_post_meta($post->ID, 'cf_speed_daemon_ignore', true);

		?>
		<input
			type="checkbox"
			name="cf_speed_daemon_ignore"
			id="cf_speed_daemon_ignore"
			value="1"
			<?php checked(1, $ignoreThis, true); ?>
		/>
		<label for="cf_speed_daemon_ignore">
			<strong><?php esc_html_e('Don\'t optimize this page', 'cf-speed-daemon'); ?></strong>
		</label>
		<?php
	}

	/**
	 * The save_post hook  callback.
	 *
	 * @param  int     $postId The post ID.
	 * @param  WP_Post $post    The post object.
	 * @return void
	 */
	public function savePostMetaBoxes($postId, $post)
	{
		$ignoreThis = filter_input(INPUT_POST, 'cf_speed_daemon_ignore');

		if (!empty($ignoreThis)) {
			update_post_meta($postId, 'cf_speed_daemon_ignore', $ignoreThis);
		} else {
			update_post_meta($postId, 'cf_speed_daemon_ignore', 0);
		}
	}

	/**
	 * Check if we're on a Speed Daemon admin page.
	 *
	 * @return boolean true if on a Speed Daemon page, false otherwise
	 */
	private function isPluginPage()
	{
		$screen = get_current_screen();
		if ('cf-speed-daemon' !== $screen->parent_base) {
			return false;
		}

		return true;
	}

	/*
	 * Add the purgin quick action to the resources table rows.
	 *
	 * @param  array   $actions An array of row action links.
	 * @param  WP_Post $post    The post object.
	 * @return array   $actions The new actions.
	 */
	public function pageTableQuickActions($actions, $post)
	{
		if (!empty($post) && ($post->post_type == 'page' || $post->post_type == 'post')) {
			$href = get_permalink($post);
			$href = add_query_arg(['cf-speed-daemon-refresh' => $post->ID], $href);

			$actions['purge'] = sprintf(
				'<a href="%1$s" target="_blank">%2$s</a>',
				esc_url($href),
				esc_html(__('Optimize CSS', 'bold'))
			);
		}
		return $actions;
	}

	/**
	 * Display relevant admin notices.
	 *
	 * @return  void
	 */
	public function showGeneralAdminNotice()
	{
		if (!$this->isPluginPage()) {
			return;
		}

		$licenseManager = new LicenseManager();
		$status = $licenseManager->getStatus();
		if ('valid' !== $status) {
			echo '<div class="notice notice-error is-dismissible">';

			if (in_array($status, ['invalid', ''])) {
				echo '<p>' . wp_kses_post(
					sprintf(
						__(
							'Speed Daemon needs a valid license to function. You can add yours on the %s page.',
							'cf-speed-daemon'
						),
						sprintf(
							/* Translators: %1 - admin license url, %2 - admin license title */
							'<a href="%1$s">%2$s</a>',
							esc_url(admin_url('admin.php?page=cf-speed-daemon-license')),
							esc_html__('License', 'cf-speed-daemon')
						)
					)
				)
					 . '</p>';
			}

			if ('disabled' === $status) {
				echo '<p>' . wp_kses_post(
					sprintf(
						__(
							'Oops!
							Looks like your Speed Daemon license key is disabled. Add a valid key on the %s page.',
							'cf-speed-daemon'
						),
						sprintf(
								/* Translators: %1 - admin license url, %2 - admin license title */
							'<a href="%1$s">%2$s</a>',
							esc_url(admin_url('admin.php?page=cf-speed-daemon-license')),
							esc_html__('License', 'cf-speed-daemon')
						)
					)
				)
					 . '</p>';
			}
			echo '</div>';
		} elseif (LicenseManager::isShowingNoCreditsLeft()) {
			echo '<div class="notice notice-warning is-dismissible">';
			echo '<p>' . wp_kses_post(
				sprintf(
					__(
						'Uh oh! Seems like your Speed Daemon license
						has no monthly credits left, so it can\'t optimize new pages.
						You can see more licensing options and check your monthly credits on the %s!',
						'cf-speed-daemon'
					),
					sprintf(
						/* Translators: %1 - admin license url, %2 - admin license title */
						'<a href="%1$s">%2$s</a>',
						esc_url(CF_SPEED_DAEMON_SHOP_SITE_URL),
						esc_html__('Crowd Favorite Shop', 'cf-speed-daemon')
					)
				)
			)
				 . '</p>';
			echo '</div>';
		}
	}
}
