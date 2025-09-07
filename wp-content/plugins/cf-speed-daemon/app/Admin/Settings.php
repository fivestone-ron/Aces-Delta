<?php

/**
 * Manages the plugin activation/deactivation and initializes the container.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

namespace CrowdFavorite\SpeedDaemonCF\Admin;

use CrowdFavorite\SpeedDaemonCF\Core\LicenseManager;

//@TODO: initialize this class and add hooks in constructor
/**
 * The main class
 */
class Settings
{

	/**
	 * Method to register settings page.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function registerSettingsPage()
	{
		$svg = '<?xml version="1.0" encoding="UTF-8"?><svg width="128px" height="128px" viewBox="0 0 128 128" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M16,95.5714286 C24.836556,95.5714286 32,102.734873 32,111.571429 L32,127.571429 L16,127.571429 C7.163444,127.571429 0,120.407985 0,111.571429 L0,95.5714286 L16,95.5714286 Z M80,47.5714286 L80,127.571429 L64,127.571429 C55.163444,127.571429 48,120.407985 48,111.571429 L48,63.5714286 C48,54.7348726 55.163444,47.5714286 64,47.5714286 L80,47.5714286 Z M128,-0.428571429 L128,111.571429 C128,120.407985 120.836556,127.571429 112,127.571429 L96,127.571429 L96,15.5714286 C96,6.73487257 103.163444,-0.428571429 112,-0.428571429 L128,-0.428571429 Z M16,47.5714286 C24.836556,47.5714286 32,54.7348726 32,63.5714286 L32,79.5714286 L16,79.5714286 C7.163444,79.5714286 0,72.4079846 0,63.5714286 L0,47.5714286 L16,47.5714286 Z" fill="black"></path></g></svg>'; //phpcs:ignore Generic.Files.LineLength.TooLong
		$icon = 'data:image/svg+xml;base64,' . base64_encode($svg);

		add_menu_page(
			esc_html__('Speed Daemon', 'cf-speed-daemon'),
			esc_html__('Speed Daemon', 'cf-speed-daemon'),
			'manage_options',
			'cf-speed-daemon',
			[$this, 'renderSettingsPage'],
			$icon
		);
		add_submenu_page(
			'cf-speed-daemon',
			esc_html__('Settings', 'cf-speed-daemon'),
			esc_html__('Settings', 'cf-speed-daemon'),
			'manage_options',
			'cf-speed-daemon',
			[$this, 'renderSettingsPage']
		);
		add_submenu_page(
			'cf-speed-daemon',
			esc_html__('License', 'cf-speed-daemon'),
			esc_html__('License', 'cf-speed-daemon'),
			'manage_options',
			'cf-speed-daemon-license',
			[$this, 'renderLicensePage']
		);
		add_submenu_page(
			'cf-speed-daemon',
			esc_html__('Stats', 'cf-speed-daemon'),
			esc_html__('Stats', 'cf-speed-daemon'),
			'manage_options',
			'cf-speed-daemon-stats',
			[$this, 'renderStatsPage']
		);
		add_submenu_page(
			'cf-speed-daemon',
			esc_html__('Help', 'cf-speed-daemon'),
			esc_html__('Help', 'cf-speed-daemon'),
			'manage_options',
			'cf-speed-daemon-help',
			[$this, 'renderHelpPage']
		);
	}

	/**
	 * Method to render settings page markup.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public static function renderSettingsPage()
	{
		load_template(CF_SPEED_DAEMON_DIR . '/views/settings.php', true);
	}

	/**
	 * Method to render license page markup.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public static function renderLicensePage()
	{
		load_template(CF_SPEED_DAEMON_DIR . '/views/license.php', true);
	}

	/**
	 * Method to render stats page markup.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public static function renderStatsPage()
	{
		load_template(CF_SPEED_DAEMON_DIR . '/views/stats.php', true);
	}

	/**
	 * Method to render help page markup.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public static function renderHelpPage()
	{
		load_template(CF_SPEED_DAEMON_DIR . '/views/help.php', true);
	}

	/**
	 * Display the section info.
	 */
	public function introSection()
	{
		?>
		<p>
			<?php
			esc_html_e(
				'Start by selecting one or more of the available operating modes:',
				'cf-speed-daemon'
			);
			?>
		</p>
		<ul>
			<li>
			<?php
				echo wp_kses_post(
					__(
						'<h4>Queue Mode</h4>
						<p class="description">This mode uses a cron job to optimize all site content.
						Depending on the number of pages on your website, this process might take a while.</p>',
						'cf-speed-daemon'
					)
				);
			?>
			</li>
			<li>
			<?php
				echo wp_kses_post(
					__(
						'<h4>Manual Mode</h4>
						<p class="description">This mode will optimize a page when saving or updating.
						You may also explicitly optimize a page by using the Speed Daemon admin bar menu.</p>',
						'cf-speed-daemon'
					)
				);
			?>
			</li>
		</ul>
		<?php
	}

	/**
	 * Display the section info for CSS Exclusion Rules.
	 */
	public function displaySectionCssExclusionRules()
	{
		echo wp_kses_post(
			sprintf(
				// Translators: %1$s is the PurgeCSS configuration page url.
				__(
					'<p class="description">Add CSS selectors to be excluded from the optimization.
					For additional information please see
					the <a href="%1$s" target="_blank">PurgeCSS documentation</a>.</p>',
					'cf-speed-daemon'
				),
				'https://purgecss.com/configuration.html#options'
			)
		);
	}

	/**
	 * Display the section info for Basic Authentication.
	 */
	public function displaySectionBasicAuthentication()
	{
		echo wp_kses_post(
			__(
				'<p class="description">If the website is password-protected using basic access authentication, it is required to provide an username and password.</p>',
				'cf-speed-daemon'
			)
		);
	}



	/**
	 * Display the section info.
	 */
	public function displayLicenseSection()
	{
		echo wp_kses_post(
			sprintf(
				__(
					'<p class="description">
						View licensing options and get a valid key on the
						<a href="%1$s" target="_blank">Crowd Favorite Shop</a>.
					</p>',
					'cf-speed-daemon'
				),
				CF_SPEED_DAEMON_SHOP_SITE_URL
			)
		);
	}

	/**
	 * Register and add settings.
	 */
	public function adminInit()
	{
		// Intro settings.
		add_settings_section(
			'cf_speed_daemon_intro',
			esc_html__('Getting Started', 'cf-speed-daemon'),
			[$this, 'introSection'],
			'cf-speed-daemon'
		);

		add_settings_field(
			'disable_css_optimisations',
			esc_html__('Disable Speed Daemon', 'cf-speed-daemon'),
			[$this, 'displayCheckbox'],
			'cf-speed-daemon',
			'cf_speed_daemon_intro',
			[
				'name' => 'disable_css_optimisations',
				'label_for' => 'cf_speed_daemon[disable_css_optimisations]'
			]
		);

		add_settings_field(
			'use_cron',
			esc_html__('Queue Mode', 'cf-speed-daemon'),
			[$this, 'displayCheckbox'],
			'cf-speed-daemon',
			'cf_speed_daemon_intro',
			[
				'name' => 'use_cron',
				'label_for' => 'cf_speed_daemon[use_cron]',
				'description' => sprintf(
					__(
						// Transtators: %1$s is the link to the WordPress cron documentation.
						'All your pages will be optimized by using a queue system.
						For additional information please see
						the <a href="%1$s" target="_blank">WordPress cron job</a> documentation.',
						'cf-speed-daemon'
					),
					'https://developer.wordpress.org/plugins/cron/'
				),
			]
		);

		add_settings_field(
			'save_event',
			esc_html__('Manual Mode', 'cf-speed-daemon'),
			[$this, 'displayCheckbox'],
			'cf-speed-daemon',
			'cf_speed_daemon_intro',
			[
				'name' => 'save_event',
				'label_for' => 'cf_speed_daemon[save_event]',
				'description' => esc_html__(
					'Your page will be optimized whenver it\'s saved or updated.
					Your may also explicitly optimize a page by using the Speed Daemon admin bar menu.',
					'cf-speed-daemon'
				),
			]
		);

		// Exclude List settings.
		add_settings_section(
			'cf_speed_daemon_http',
			esc_html__('Basic Authentication', 'cf-speed-daemon'),
			[$this, 'displaySectionBasicAuthentication'],
			'cf-speed-daemon'
		);

		add_settings_field(
			'http_basic_auth',
			esc_html__('Is site access password-protected?', 'cf-speed-daemon'),
			[$this, 'displayCheckbox'],
			'cf-speed-daemon',
			'cf_speed_daemon_http',
			[
				'name' => 'http_basic_auth',
				'label_for' => 'cf_speed_daemon[http_basic_auth]'
			]
		);

		add_settings_field(
			'http_basic_auth_username',
			esc_html__('Username', 'cf-speed-daemon'),
			[$this, 'displayInput'],
			'cf-speed-daemon',
			'cf_speed_daemon_http',
			[
				'name' => 'http_basic_auth_username',
				'label_for' => 'cf_speed_daemon[http_basic_auth_username]'
			]
		);

		add_settings_field(
			'http_basic_auth_password',
			esc_html__('Password', 'cf-speed-daemon'),
			[$this, 'displayPasswordInput'],
			'cf-speed-daemon',
			'cf_speed_daemon_http',
			[
				'name' => 'http_basic_auth_password',
				'label_for' => 'cf_speed_daemon[http_basic_auth_password]'
			]
		);

		// Exclude List settings.
		add_settings_section(
			'cf_speed_daemon',
			esc_html__('CSS Exclusion Rules', 'cf-speed-daemon'),
			[$this, 'displaySectionCssExclusionRules'],
			'cf-speed-daemon'
		);

		add_settings_field(
			'cf_speed_daemon',
			esc_html__('Exclude the following selectors:', 'cf-speed-daemon'),
			[$this, 'displayTextarea'],
			'cf-speed-daemon',
			'cf_speed_daemon',
			[
				'name' => 'exclude_list',
				'description' => wp_kses_post(__(
					'Define CSS selectors that should be ignored when Speed Daemon optimizes your page CSS.
					For example the <strong>button</strong> rule will exclude <strong>.button</strong>,
					<strong>#button</strong> and the html element <strong>button</strong>.
					For finer control use <strong>Exclude List Patterns</strong>.
					Add one selector per line.',
					'cf-speed-daemon'
				))
			]
		);

		add_settings_field(
			'exclude_list_patterns',
			esc_html__('Exclude the following patterns:', 'cf-speed-daemon'),
			[$this, 'displayTextarea'],
			'cf-speed-daemon',
			'cf_speed_daemon',
			[
				'name' => 'exclude_list_patterns',
				'description' => wp_kses_post(__(
					'Exclude CSS selectors based on a regular expression.
					For example <strong>^primary-</strong> will exclude <strong>.primary-color</strong>, while
					<strong>-primary$</strong> will exclude <strong>.button-primary</strong>. Add one rule per line.',
					'cf-speed-daemon'
				))
			]
		);

		add_settings_field(
			'exclude_list_patterns_children',
			esc_html__('Exclude the following recursive patterns:', 'cf-speed-daemon'),
			[$this, 'displayTextarea'],
			'cf-speed-daemon',
			'cf_speed_daemon',
			[
				'name' => 'exclude_list_patterns_children',
				'description' => __(
					'Exclude CSS selectors based on a regular expression.
					Contrary to regular exclusion patterns,
					these will <strong>also exclude children of the selectors</strong>.
					Add one rule per line.',
					'cf-speed-daemon'
				)
			]
		);

		add_settings_field(
			'exclude_handles',
			esc_html__('Exclude the following handles (advanced):', 'cf-speed-daemon'),
			[$this, 'displayTextarea'],
			'cf-speed-daemon',
			'cf_speed_daemon',
			[
				'name' => 'exclude_handles',
				'description' => wp_kses_post(__( //phpcs:ignore
					'Exclude stylesheets from being optimized by Speed Daemon via their handle.
					For reference, the following stylesheet has the handle <strong>example</strong>:' .
					//phpcs:ignore
					'<pre><link rel="stylesheet" id="<strong>example</strong>-css" href="..."></pre>
					Only one handle must be added per line.',
					'cf-speed-daemon'
				))
			]
		);

		register_setting(
			'cf_speed_daemon',
			'cf_speed_daemon',
			['sanitize_callback' => [$this, 'sanitizeFields']]
		);

		// License section
		add_settings_section(
			'cf_speed_daemon_license',
			esc_html__('Activation', 'cf-speed-daemon'),
			[$this, 'displayLicenseSection'],
			'cf-speed-daemon-license'
		);

		add_settings_field(
			'cf_speed_daemon_api_key',
			esc_html__('License Key', 'cf-speed-daemon'),
			[$this, 'displayLicenseField'],
			'cf-speed-daemon-license',
			'cf_speed_daemon_license',
			[
				'name' => 'api_key',
				'label_for' => 'cf_speed_daemon_license[api_key]',
				'class' => 'cf_speed_daemon_license_api_key',
			]
		);

		register_setting(
			'cf_speed_daemon_license',
			'cf_speed_daemon_license',
			['sanitize_callback' => [$this, 'checkLicense']]
		);
	}

	/**
	 * Sanitize textarea fields values.
	 *
	 * @param array $field_values Field values.
	 *
	 * @return array
	 */
	public function sanitizeFields($field_values)
	{
		foreach ($field_values as $key => $value) {
			$field_values[$key] = sanitize_textarea_field($value);
		}
		return $field_values;
	}

	/**
	 * Add markup for textarea.
	 *
	 * @param array $args Arguments.
	 */
	public function displayTextarea($args)
	{
		if (empty($args['name'])) {
			return;
		}

		$option_value = self::getOptionValue($args['name']);

		printf(
			/* Translators: %1$s - textarea name; %2$s - textarea id; %3$s - textarea value */
			'<textarea name="cf_speed_daemon[%1$s]" id="%2$s" class="regular-text code" rows="10">%3$s</textarea>',
			esc_attr($args['name']),
			esc_attr($args['name']),
			wp_kses_post($option_value)
		);

		if (!empty($args['description'])) {
			printf(
				'<p class="description" id="%1$s-description">%2$s</p>',
				esc_attr($args['name']),
				wp_kses_post($args['description'])
			);
		}
	}

	/**
	 * Add markup for password input.
	 *
	 * @param array $args Args.
	 */
	public function displayPasswordInput($args)
	{
		$this->displayInput(
			array_merge(
				$args,
				[
					'type' => 'password'
				]
			)
		);
	}

	/**
	 * Add markup for input.
	 *
	 * @param array $args Arguments.
	 */
	public function displayInput($args)
	{
		$type = 'text';
		if (!empty($args['type'])) {
			$type = $args['type'];
		}

		if (empty($args['name'])) {
			return;
		}

		$option_value = trim(self::getOptionValue($args['name'], 'cf_speed_daemon'));

		?>
		<input
			type="<?php echo esc_attr($type); ?>"
			name="cf_speed_daemon[<?php echo esc_attr($args['name']); ?>]"
			id="cf_speed_daemon[<?php echo esc_attr($args['name']); ?>]"
			class="<?php echo esc_attr($args['class']); ?>"
			value="<?php echo esc_attr($option_value);?>"
		/>
		<?php

		if (!empty($args['description'])) {
			printf(
				'<p class="description" id="%1$s-description">%2$s</p>',
				esc_attr($args['name']),
				wp_kses_post($args['description'])
			);
		}
	}

	/**
	 * Add markup for license field.
	 *
	 * @param array $args Arguments.
	 */
	public function displayLicenseField($args)
	{
		if (empty($args['name'])) {
			return;
		}

		$option_value = trim(self::getOptionValue($args['name'], 'cf_speed_daemon_license'));
		$masked = str_pad(
			substr($option_value, 0, 4),
			4 + 7 * (strlen($option_value)  - 4),
			'&#9679;',
			STR_PAD_RIGHT
		);
		$status = self::getOptionValue('status', 'cf_speed_daemon_license');
		if ($status === 'valid') {
			$option_value = $masked;
			$status_notice = esc_html__('Valid license key added.', 'cf-speed-daemon');
		} elseif (!empty($option_value)) {
			$status_notice = esc_html__('Uh oh! That\'s an invalid license key.', 'cf-speed-daemon');
		}
		?>

		<input
			type="text"
			name="cf_speed_daemon_license[<?php echo esc_attr($args['name']); ?>]"
			id="cf_speed_daemon_license[<?php echo esc_attr($args['name']); ?>]"
			class="<?php echo esc_attr($args['class']); ?>"
			value="<?php echo esc_attr($option_value);?>"
		/>
		<?php

		if (!empty($args['description'])) {
			printf(
				'<p class="description" id="%1$s-description">%2$s</p>',
				esc_attr($args['name']),
				wp_kses_post($args['description'])
			);
		}

		printf(
			'<p class="description" id="%1$s-description-status">%2$s</p>',
			esc_attr($args['name']),
			wp_kses_post($status_notice)
		);
	}

	/**
	 * Add markup for checkbox.
	 *
	 * @param array $args Arguments.
	 */
	public function displayCheckbox($args)
	{
		if (empty($args['name'])) {
			return;
		}

		$option_value = self::getOptionValue($args['name']);

		?>
		<input
			type="checkbox"
			name="cf_speed_daemon[<?php echo esc_attr($args['name']); ?>]"
			id="cf_speed_daemon[<?php echo esc_attr($args['name']); ?>]"
			value="1"
			<?php checked(1, $option_value, true); ?>
			/>
		<?php

		if (!empty($args['description'])) {
			printf(
				'<p class="description" id="%1$s-description">%2$s</p>',
				esc_attr($args['name']),
				wp_kses_post($args['description'])
			);
		}
	}

	/**
	 * Get option value from DB.
	 *
	 * @param string $field_name Field name.
	 *
	 * @return string
	 */
	public static function getOptionValue($field_name, $option_name = null): string
	{
		$options = get_option($option_name ?? 'cf_speed_daemon');

		if (empty($options[ $field_name ])) {
			return '';
		}

		return $options[ $field_name ];
	}

	/**
	 * Get the list values by spliting strings by new line separator and eliminating empty strings from array.
	 *
	 * @param string $fieldName Field name.
	 *
	 * @return array
	 */
	public static function getListValuesFromOption($fieldName)
	{
		$listValue = Settings::getOptionValue($fieldName);

		if (empty($listValue)) {
			return [];
		}

		return array_filter(array_map('trim', explode(PHP_EOL, $listValue)));
	}

	/**
	 * Check license if it's valid or not.
	 *
	 * @param array $fieldValues Field values.
	 *
	 * @return array
	 */
	public function checkLicense($fieldValues)
	{
		$licenseManager = new LicenseManager();
		if ($licenseManager->activateLicense($fieldValues['api_key'])) {
			$fieldValues['status'] = 'valid';
			$fieldValues['domain'] = $licenseManager->getHost();
		} else {
			$fieldValues['status'] = 'invalid';
			$fieldValues['domain'] = '';
		}

		LicenseManager::hideNoCreditsLeft();

		return $fieldValues;
	}

	public function updateLicenseStatus($newStatus): bool
	{
		$license = get_option('cf_speed_daemon_license', true);

		$license['status'] = $newStatus;
		return update_option('cf_speed_daemon_license', $license);
	}
}
