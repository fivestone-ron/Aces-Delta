<?php

/**
 * Manages license handling by making requests to activate.
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use CrowdFavorite\SpeedDaemonCF\Admin\Settings;

/**
 * Class LicenseManager
 * @package CrowdFavorite\SpeedDaemonCF\Core
 */
class LicenseManager
{

	/**
	 * Option name for no credits left.
	 *
	 * const string
	 */
	public const NO_CREDITS_LEFT_OPTION_NAME = 'cf_speed_daemon_license_no_credits_left_notice';

	/**
	 * Make request to activate license.
	 *
	 * @param string $apiKey Api Key.
	 *
	 * @return bool
	 */
	public function activateLicense($apiKey)
	{
		$activationUrl = apply_filters(
			'cf_speed_daemon_rest_api_base_url',
			CF_SPEED_DAEMON_REST_API_BASE_URL
		) . '/activate';

		$request = new RestApi();
		$response = $request->doRequest($activationUrl, [
			'website' => $this->getHost(),
			'apiKey' => $apiKey
		]);

		$data = json_decode($response, true);

		return !empty($data['license']) && 'valid' === $data['license'];
	}

	/**
	 * Get host from site url.
	 *
	 * @return string
	 */
	public function getHost(): string
	{
		$parse = parse_url(get_site_url());
		return $parse['host'];
	}

	/**
	 * Is license activated?
	 *
	 * @return bool
	 */
	public static function isActivated(): bool
	{
		$status = Settings::getOptionValue('status', 'cf_speed_daemon_license');

		return 'valid' === $status;
	}

	/**
	 * Get activation status.
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return Settings::getOptionValue('status', 'cf_speed_daemon_license') ?? 'invalid';
	}

	/**
	 * Set license status as disabled.
	 */
	public static function disableLicense()
	{
		$settings = new Settings();
		$settings->updateLicenseStatus('disabled');
	}

	/**
	 * Show no credits left.
	 */
	public static function showNoCreditsLeft()
	{
		set_transient(self::NO_CREDITS_LEFT_OPTION_NAME, 1, DAYS_IN_SECONDS);
	}

	/**
	 * Hide no credits left.
	 */
	public static function hideNoCreditsLeft()
	{
		delete_transient(self::NO_CREDITS_LEFT_OPTION_NAME, 0);
	}

	/**
	 * Is showing no credits left.
	 */
	public static function isShowingNoCreditsLeft()
	{
		return !empty(get_transient(self::NO_CREDITS_LEFT_OPTION_NAME));
	}
}
