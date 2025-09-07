<?php

/**
 * Facade class for REST API calls to Speed Daemon by Crowd Favorite server.
 *
 * @package cf-speed-daemon
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use WP_Error;

/**
 * Class RestApi
 *
 * @package CrowdFavorite\SpeedDaemonCF\Core
 */
class RestApi
{
	/**
	 * Do request.
	 *
	 * @param string $url          Url.
	 * @param array  $requestBody Request body.
	 *
	 * @return string|WP_Error
	 */
	public function doRequest($url, $requestBody, $authCredentials = [])
	{
		$header = [
			'Content-Type'  => 'application/json; charset=utf-8',
			'Expect'        => '',
		];

		if (!empty($authCredentials)) {
			$header['Authorization'] = $this->getAuthorizationHeader($authCredentials);
		}

		$response = wp_remote_post(
			$url,
			[
				'headers'     => $header,
				'blocking'    => true,
				'timeout'     => 60,
				'redirection' => 5,
				'body'        => wp_json_encode($requestBody),
			]
		);

		$httpResponseCode = wp_remote_retrieve_response_code($response);
		if (200 !== $httpResponseCode) {
			$filesystem = Filesystem::getInstance();
			$filesystem->log(
				sprintf('Request responded with http code status %d %s', $httpResponseCode, $url),
				$response
			);

			if (403 === $httpResponseCode) {
				$responseBody = wp_remote_retrieve_body($response);
				$body = json_decode($responseBody, true);
				if ($body['license'] === 'invalid') {
					return new WP_Error(
						'invalid',
						'Invalid license'
					);
				} elseif ($body['license'] === 'disabled') {
					return new WP_Error(
						'disabled',
						'Disabled license'
					);
				} elseif ($body['license'] === 'zero_credits_left') {
					return new WP_Error(
						'zero_credits_left',
						'No credits left'
					);
				}
			}
			return new WP_Error();
		}

		return wp_remote_retrieve_body($response);
	}

	/**
	 * Get processed styles.
	 *
	 * @param string $url          Url.
	 * @param array  $request_body Request body.
	 *
	 * @return mixed|string|WP_Error
	 */
	public function getStyles($url, $request_body, $authCredentials = [])
	{
		$response_body = $this->doRequest($url, $request_body, $authCredentials);

		if (is_wp_error($response_body)) {
			return $response_body;
		}

		return json_decode($response_body, true);
	}

	/**
	 * Get authorization header.
	 *
	 * @return string
	 */
	public function getAuthorizationHeader($authCredentials)
	{
		return 'Basic ' . base64_encode(sprintf('%1$s:%2$s', $authCredentials['domain'], $authCredentials['apiKey']));
	}
}
