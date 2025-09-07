<?php

/**
 * Manages the purging process by parsing the post page, sending the css assets to the endpoint for purging.
 *
 * @package cf-speed-daemon
 */

namespace CrowdFavorite\SpeedDaemonCF\Core;

use CrowdFavorite\SpeedDaemonCF\Admin\Settings;
use WP_Post;

/**
 * Class Purger
 */
class Purger
{

	/**
	 * The meta key.
	 */
	public const PURGER_META_KEY = 'cf_speed_daemon_cron_status';

	/**
	 * The file meta key.
	 */
	public const PURGER_FILENAME_META_KEY = 'cf_speed_daemon_filename';

	/**
	 * The meta key for purging errors.
	 */
	public const PURGER_ERROR = 'cf_speed_daemon_error';

	/**
	 * Is the url the same domain as the site.
	 *
	 * @param string $url Url.
	 *
	 * @return bool
	 */
	public function isSameDomain(string $url): bool
	{
		$siteUrlParsed = parse_url(get_site_url());
		$currentUrlParsed = parse_url($url);

		return empty($currentUrlParsed['host']) || $currentUrlParsed['host'] === $siteUrlParsed['host'];
	}

	/**
	 * Make a remote request with authorization header.
	 *
	 * @param string $url Url to load.
	 *
	 * @return array|\WP_Error Return array if succesful (http status code 200) or if not, return error.
	 */
	public function remoteRequest(string $url)
	{
		$args = null;

		// Only add authorization headers if the url is from the same url.
		if (
			Settings::getOptionValue('http_basic_auth') === '1'
			&& $this->isSameDomain($url)
		) {
			$httpBasicAuthUsername = Settings::getOptionValue('http_basic_auth_username');
			$httpBasicAuthPassword = Settings::getOptionValue('http_basic_auth_password');

			$args = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode($httpBasicAuthUsername . ':' . $httpBasicAuthPassword)
				)
			);
		}

		$response = wp_remote_get($url, $args);

		if (200 !== wp_remote_retrieve_response_code($response)) {
			$cleanUrl = str_replace('&cf-speed-daemon=1', '', $url);

			$filesystem = Filesystem::getInstance();
			$filesystem->log(
				sprintf('Cannot purge page %s', $cleanUrl),
				wp_remote_retrieve_response_message($response)
			);

			return new \WP_Error(
				wp_remote_retrieve_response_code($response),
				wp_remote_retrieve_response_message($response)
			);
		}

		return $response;
	}

	/**
	 * Purge the custom url.
	 *
	 * @param string $url The custom url.
	 */
	public function purgeUrl($url)
	{
		$filesystem = Filesystem::getInstance();
		$url        = add_query_arg('cf-speed-daemon', '1', $url);
		$urlId     = 'test';

		// Process only the requests not made by us to avoid loop.
		if (! filter_input(INPUT_GET, 'cf-speed-daemon')) {
			// Get the page content.
			$response = $this->remoteRequest($url);

			if (is_array($response) && ! is_wp_error($response)) {
				$pageContent = $response['body'];

				$styles = $this->getPageStyles($pageContent);

				if (empty($styles)) {
					return false;
				}

				$optimizedStyles = $this->getOptimizedStyles($pageContent, $styles);

				if (! empty($optimizedStyles) && ! is_wp_error($optimizedStyles)) {
					$mergedStyles = implode(
						PHP_EOL,
						array_map(
							function ($style) {
								return $style['css'];
							},
							$optimizedStyles
						)
					);

					// The file name without the extension.
					$fileName = apply_filters('cf_speed_daemon_filename', 'url-' . $urlId, $post);


					if (
						$filesystem->write($fileName . '.css', $mergedStyles)
					) {
						// TODO: Save the url into an option.
					}
				}
			} else {
				$filesystem->log(
					sprintf('Cannot load page %s', $url),
					$response
				);
			}
		}
	}

	/**
	 * Purge the post.
	 *
	 * @param WP_Post $post Post.
	 */
	public function purgePost($post)
	{
		$filesystem = Filesystem::getInstance();
		$postId     = $post->ID;
		$postLink   = get_permalink($postId);
		$postLink   = add_query_arg('cf-speed-daemon', '1', $postLink);
		$time       = get_post_time('Y-m-d H:i:s', true, $postId);

		// Process only the requests not made by the plugin to avoid loops.
		if (! filter_input(INPUT_GET, 'cf-speed-daemon')) {
			// Get the page content.
			$response = $this->remoteRequest($postLink);

			if (is_wp_error($response)) {
				update_post_meta($postId, self::PURGER_ERROR, $response->get_error_messages());
			}

			if (is_array($response) && !is_wp_error($response)) {
				delete_post_meta($postId, self::PURGER_ERROR);
				$pageContent = $response['body'];

				$styles = $this->getPageStyles($pageContent);

				if (empty($styles)) {
					return false;
				}

				$mergedCss = array_reverse($styles['cascadingCSS']);
				$optimizedStyles = $this->getOptimizedStyles($pageContent, $mergedCss);

				if (! empty($optimizedStyles) && ! is_wp_error($optimizedStyles)) {
					$mergedStyles = implode(
						PHP_EOL,
						array_map(
							function ($style) {
								return $style['css'];
							},
							$optimizedStyles
						)
					);

					// The file name without the extension.
					$fileName = apply_filters('cf_speed_daemon_filename', 'post-' . $postId, $post);

					if (
						$filesystem->write($fileName . '.css', $mergedStyles, $time)
					) {
						update_post_meta($postId, self::PURGER_META_KEY, date_i18n('Y-m-d h:i:s'));

						$stats = new Stats($postId);
						$stats->save($mergedCss, $mergedStyles);

						clean_post_cache($postId);

						// Trigger a 'save_post' action to force cache clearing for the current post
						// on systems implementing page caching and cache clearing on post save.
						$post = get_post($postId);
						if (is_a($post, \WP_Post::class)) {
							// Remove post purging trigger after post update, which is trigged by a post purge.
							remove_all_actions('purgePostOnSave');

							do_action('save_post', $postId, $post, true);
						}

						// The post was optimised. Generate a specific action for other plugins.
						do_action('cf_speed_daemon_post_optimised', $postId);
					}
				}
			} else {
				$filesystem->log(
					sprintf(
						'Cannot load page %s',
						$postLink
					),
					$response
				);
			}
		}
	}

	/**
	 * Purge the term.
	 *
	 * @param WP_Term $term Term.
	 */
	public function processTerm($term)
	{
		$filesystem = Filesystem::getInstance();
		$term_id = $term->term_id;
		$link    = get_term_link($term);
		if (empty($link) || is_wp_error($link)) {
			return false;
		}
		$link = add_query_arg('cf-speed-daemon', '1', $link);

		// Process only the requests not made by us to avoid loop.
		if (! filter_input(INPUT_GET, 'cf-speed-daemon')) {
			// Get the page content.
			$response = $this->remoteRequest($link);

			if (is_array($response) && ! is_wp_error($response)) {
				$pageContent = $response['body'];

				$styles = $this->getPageStyles($pageContent);

				if (empty($styles)) {
					return false;
				}

				$mergedCss = array_reverse($styles['cascadingCSS']);
				$optimizedStyles = $this->getOptimizedStyles($pageContent, $mergedCss);

				if (! empty($optimizedStyles) && ! is_wp_error($optimizedStyles)) {
					$mergedStyles = implode(
						PHP_EOL,
						array_map(
							function ($style) {
								return $style['css'];
							},
							$optimizedStyles
						)
					);

					$fileName = apply_filters('cf_speed_daemon_filename', 'term-' . $term_id, $term);

					if (
						$filesystem->write($fileName . '.css', $mergedStyles)
					) {
						update_term_meta($term_id, self::PURGER_META_KEY, date_i18n('Y-m-d h:i:s'));

						/**
						 * TODO: Make stats work for terms.
						 * $stats = new Stats( $post->ID );
						 * $stats->save( $mergedCss, $mergedStyles );
						 */
					}
				}
			} else {
				$filesystem->log(
					sprintf('Cannot load page %s', $link),
					$response
				);
			}
		}
	}

	/**
	 * Add exclude list params (excludeList, excludeListPatterns, excludeListPatternsChildren) to request data.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	protected function maybeAddExcludeLists($args)
	{
		$excludeList = $this->getExcludeListValues('exclude_list');
		if (count($excludeList)) {
			$args['excludeList'] = $excludeList;
		}

		$excludeListPatterns = $this->getExcludeListValues('exclude_list_patterns');
		if (count($excludeListPatterns)) {
			$args['excludeListPatterns'] = $excludeListPatterns;
		}

		$excludeListPatternsChildren = $this->getExcludeListValues('exclude_list_patterns_children');
		if (count($excludeListPatternsChildren)) {
			$args['excludeListPatternsChildren'] = $excludeListPatternsChildren;
		}

		return $args;
	}


	/**
	 * Get exclude list values by spliting strings by new line separator and eliminating empty strings from array.
	 *
	 * @param string $fieldName Field name.
	 *
	 * @return array
	 */
	protected function getExcludeListValues($fieldName)
	{
		$excludeListValue = Settings::getOptionValue($fieldName);

		if (empty($excludeListValue)) {
			return [];
		}

		return array_filter(array_map('trim', explode(PHP_EOL, $excludeListValue)));
	}

	/**
	 * Add the filename meta for the object.
	 *
	 * @param WP_Post/WP_Term/WP_User $object The object to be marked.
	 */
	protected function addObjectFilemeta($object)
	{
		if (get_class($object) === 'WP_Post') {
			$objectType = $object->post_type;
			$objectId   = $object->ID;
			update_post_meta(
				$objectId,
				self::PURGER_FILENAME_META_KEY,
				$objectType
			);
		} elseif (get_class($object) === 'WP_Term') {
			$objectType = $object->taxonomy;
			$objectId   = $object->term_id;
			update_term_meta(
				$objectId,
				self::PURGER_FILENAME_META_KEY,
				$objectType
			);
		} elseif (get_class($object) === 'WP_User') {
			$objectType = 'author';
			$objectId   = $object->ID;
			update_user_meta(
				$objectId,
				self::PURGER_FILENAME_META_KEY,
				$objectType
			);
		}
	}

	/**
	 * Make the general style file for the type of the objects.
	 *
	 * @param  WP_Post/WP_Term/WP_User $object       The object to create the type style.
	 * @param  string                  $mergedStyles The object optimized style.
	 * @return void
	 */
	public function maybeCreateTypeStyle($object, $mergedStyles)
	{
		$objectType = false;
		$object_id   = false;
		$filesystem  = Filesystem::getInstance();

		if (get_class($object) === 'WP_Post') {
			$objectType = $object->post_type;
			$object_id   = $object->ID;
		} elseif (get_class($object) === 'WP_Term') {
			$objectType = $object->taxonomy;
			$object_id   = $object->term_id;
		} elseif (get_class($object) === 'WP_User') {
			$objectType = 'author';
			$object_id   = $object->ID;
		}

		if (
			! empty($objectType)
			&& ! $filesystem->exists($objectType . '.css')
		) {
			$filesystem->write($objectType . '.css', $mergedStyles);
			$this->addObjectFilemeta($object);
		} else {
			$object_type_css = $filesystem->get_contents($objectType . '.css');

			// Check if the classes are already in the post type css file.
			if (
				! empty($object_type_css)
				&& ! $this->areEqualOrContains($mergedStyles, $object_type_css)
				&& $this->areEqualOrContains($object_type_css, $mergedStyles)
			) {
				// Rewrite the file with the bigger yet smaller css.
				$filesystem->write($objectType . '.css', $mergedStyles);
				$this->addObjectFilemeta($object);
			}
		}
	}

	/**
	 * Process the page content and return the stylings.
	 *
	 * @param  string $pageContent The page content.
	 * @return array               The cascading styling.
	 */
	private function getPageStyles($pageContent)
	{
		$cascadingCSS  = [];
		$dom           = new \DomDocument();
		$siteUrlParsed = parse_url(get_site_url());

		// Clear errors list if any.
		libxml_clear_errors();
		// Use internal errors, don't spill out warnings.
		$previous = libxml_use_internal_errors(true);

		$dom->loadHTML($pageContent);

		$elements = $dom->getElementsByTagName('style');
		for ($i = $elements->length; --$i >= 0;) {
			$cssFileContent = $this->replaceRelativeUrl($elements->item($i)->textContent);
			$cascadingCSS[] = $cssFileContent;
		}

		$items = $dom->getElementsByTagName('link');
		for ($i = $items->length; --$i >= 0;) {
			if ('stylesheet' === $items->item($i)->attributes->getNamedItem('rel')->nodeValue) {
				$fileUrl = $items->item($i)->attributes->getNamedItem('href')->nodeValue;
				if (empty($fileUrl)) {
					continue;
				}

				if (
					$items->item($i)->attributes->getNamedItem('id')
					&& $this->isHandleExcluded($items->item($i)->attributes->getNamedItem('id')->nodeValue)
				) {
					continue;
				}

				// Fix urls which start with //, which cannot be loaded by wp_remote_get.
				if (strpos($fileUrl, '//') === 0) {
					$fileUrl = $siteUrlParsed['scheme'] . ':' . $fileUrl;
				}

				$file = $this->remoteRequest($fileUrl);

				if (200 === wp_remote_retrieve_response_code($file)) {
					$cssFileContent = wp_remote_retrieve_body($file);
					$cssFileContent = $this->replaceRelativeUrl($cssFileContent, $fileUrl);

					if (strlen($cssFileContent)) {
						$cascadingCSS[] = $cssFileContent;
					}
				}
			}
		}

		// Clear errors list if any.
		libxml_clear_errors();
		// Restore previous behavior.
		libxml_use_internal_errors($previous);

		return ['cascadingCSS' => $cascadingCSS];
	}

	/**
	 * If the selectors are already in the haystack.
	 *
	 * @param  string $needle   The css to be checked.
	 * @param  string $haystack The css to be checked against.
	 * @return boolean          If the haystack contains the selectors or not.
	 */
	public function areEqualOrContains($needle, $haystack)
	{
		$needle   = $this->getCssSelectors($needle);
		$haystack = $this->getCssSelectors($haystack);

		if (in_array($needle, $haystack, true)) {
			return true;
		}
		return false;
	}

	/**
	 * Get the css classes from a css file content.
	 *
	 * @param  string $css The css as text.
	 * @return array       The classes.
	 */
	public function getCssSelectors($css)
	{
		$rules = [];
		$css   = str_replace(
			['\r', '\n', '\t', ' '],
			'',
			$css
		);

		$matches = explode('}', $css);

		// If a } didn't exist then we probably don't have a valid CSS file.
		if ($matches) {
			// Loop each item.
			foreach ($matches as $v) {
				// Explode on the opening curly brace and the ZERO index should be the class declaration.
				$second = explode('{', $v);

				// The final item in $first is going to be empty so we should ignore it.
				if (isset($second[0]) && '' !== $second[0]) {
					$rules[] = trim($second[0]);
				}
			}
		}

		return $rules;
	}

	/**
	 * Get the optimized styles.
	 *
	 * @param  array $pageContent     The page content.
	 * @param  array $mergedCss       The css styles.
	 * @return array $optimizedStyles The css styles optimized.
	 */
	public function getOptimizedStyles($pageContent, $mergedCss)
	{
		$connection = new RestApi();

		$args = [
			'content' => [
				[
					'raw'       => $pageContent,
					'extension' => 'html',
				],
			],
			'css'     => $mergedCss,
			'minify'  => true,
		];

		$args = $this->maybeAddExcludeLists($args);

		// Send data to Processor REST API endpoint.
		$optimizedStyles = $connection->getStyles(
			apply_filters('cf_speed_daemon_rest_api_base_url', CF_SPEED_DAEMON_REST_API_BASE_URL) . '/page',
			$args,
			[
				'domain' => Settings::getOptionValue('domain', 'cf_speed_daemon_license'),
				'apiKey' => Settings::getOptionValue('api_key', 'cf_speed_daemon_license'),
			]
		);

		if (is_wp_error($optimizedStyles)) {
			$errorCode = $optimizedStyles->get_error_code();
			if (in_array($errorCode, ['disabled', 'invalid'])) {
				LicenseManager::disableLicense();
			} elseif ('zero_credits_left' === $errorCode) {
				LicenseManager::showNoCreditsLeft();
			}
		} else {
			LicenseManager::hideNoCreditsLeft();
		}

		return $optimizedStyles;
	}

	/**
	 * Replace the relative url from css content to absolute path.
	 *
	 * @param  string $cssContent The css content.
	 * @param  string $path       The local domain path.
	 * @return string             The new css content.
	 */
	public function replaceRelativeUrl($cssContent, $path = null)
	{
		if (!empty($path)) {
			// Get the url path.
			$path = parse_url($path, PHP_URL_PATH);
			// Trim first and last slash.
			$path = trim($path, '/');
			// Build the absolute local path.
			$path = get_site_url() . '/' . dirname($path) . '/';
		} else {
			$path = get_site_url() . '/';
		}

		$replace['/url[\s]*\([\s]*"[\s]*(?!https?:\/\/)(?!\/\/)(?!data:)(?!#)/i']   = 'url("' . $path;
		$replace["/url[\s]*\([\s]*'[\s]*(?!https?:\/\/)(?!\/\/)(?!data:)(?!#)/i"]   = "url('" . $path;
		$replace["/url[\s]*\([\s]*(?!'|\")(?!https?:\/\/)(?!\/\/)(?!data:)(?!#)/i"] = 'url(' . $path;

		$cssContent = preg_replace(
			array_keys($replace),
			array_values($replace),
			$cssContent
		);
		return $cssContent;
	}

	/**
	 * Get purge errors.
	 *
	 * @param int $postId Post id.
	 *
	 * @return string
	 */
	public function getErrors($postId)
	{
		// No credits left takes precedence over any kind of other errors.
		if (LicenseManager::isShowingNoCreditsLeft()) {
			return esc_html__('No credits left!', 'cf-speed-daemon');
		}

		return implode(',', (array)get_post_meta($postId, self::PURGER_ERROR, true));
	}

	/**
	 * Is handle excluded from settings to be purged.
	 * WordPress appends `-css` to the handle name when embedding it on the page,
	 * so checking for both versions is useful.
	 *
	 * @param string $handle Handle or link tag Id.
	 *
	 * @return bool
	 */
	public function isHandleExcluded($handle): bool
	{
		$trimmedHandle = str_replace('-css', '', $handle);
		$excludedHandles = Settings::getListValuesFromOption('exclude_handles');
		return in_array($handle, $excludedHandles) || in_array($trimmedHandle, $excludedHandles);
	}
}
