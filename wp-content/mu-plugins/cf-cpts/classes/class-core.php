<?php

/**
 * Core class.
 *
 * @package cf-cpts
 */

namespace CrowdFavorite\CPTs;

/**
 * Core class.
 */
class Core
{
	/**
	 * Active taxonomy models.
	 *
	 * @access public
	 *
	 * @var null|array An array of active taxonomy models or null to use the default ones.
	 */
	public $active_taxonomy_models = null;

	/**
	 * The list of WP core post type models.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $core_post_type_models = [
		'post',
		'page',
		'attachment',
	];

	/**
	 * The list of cpt models.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $cpt_models = [];

	/**
	 * Disabled CPTs.
	 *
	 * All CPTs are enabled by default.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $disabled_cpts_models = [];

	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Core
	 */
	private static $instance;

	/**
	 * The list of taxonomy models.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $taxonomy_models = [];

	/**
	 * The base property of the current screen (WP_Screen).
	 * @var string
	 */
	private $currentScreenBase = '';

	/**
	 * Mark if the screen base changed in the current request.
	 * @var boolean
	 */
	private $screenBaseChanged = false;

	/**
	 * Store the list of screens where the Global Media sould be forced to display.
	 * @var array
	 */
	private $globalMediaAcfScreens = [
		[
			'id' => 'edit-product_category',
		],
	];

	/**
	 * Initialize data; register hooks.
	 *
	 * @access public
	 */
	public function __construct()
	{
		$this->disabled_cpts_models = get_option('cf_disabled_cpts_models', []);
		$this->active_taxonomy_models = get_option('cf_taxonomies_models', null);

		$this->loadCPTModels();
		$this->loadTaxonomyModels();

		add_action('init', [$this, 'updateSettings']);
		add_action('admin_menu', [$this, 'registerSettingsPage']);
		add_action('admin_menu', [$this, 'insertReusableBlocksMenu']);
		add_filter('parent_file', [$this, 'reusableBlocksMenuParentFile']);
		add_action('admin_enqueue_scripts', [$this, 'forceLoadGlobalMediaJs']);
		add_action('admin_enqueue_scripts', [$this, 'afterForceLoadGlobalMediaJs'], 11);

		add_filter(
			'multilingualpress.post_meta_data',
			[$this, 'multilingualPressPostMetaData'],
			10,
			2
		);

		add_filter(
			'multilingualpress.sync_post_meta_keys',
			[$this, 'multilingualPressSyncPostMetaKeys'],
			10,
			3
		);
	}

	/**
	 * Get an array of the core post type models and the custom post type models.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getCombinedPostTypeModels()
	{
		return array_merge($this->core_post_type_models, $this->cpt_models);
	}

	/**
	 * Get active object instance.
	 *
	 * @access public
	 * @static
	 *
	 * @return Core
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get model class name.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $type Model type (CPTs, Taxonomies).
	 * @param string $model Model name.
	 *
	 * @return string Model class name.
	 */
	public static function getModelClassName($type, $model)
	{
		// Convert dashes and underscores with spaces, so that we can capitalize words.
		$model = ucwords(str_replace(['-', '_'], ' ', $model));

		// Remove all spaces.
		$model = preg_replace('/\s+/', '', $model);

		return __NAMESPACE__ . '\\' . $type . '\\' . ucwords($model);
	}

	/**
	 * Get taxonomy assigned object types.
	 *
	 * @access public
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array
	 */
	public function getTaxonomyObjectTypes($taxonomy)
	{
		$blog_id = get_current_blog_id();

		// The active taxonomies were not altered. Return the default if set.
		if (null === $this->active_taxonomy_models) {
			$model_class = self::getModelClassName('Taxonomies', $taxonomy);

			if (!empty($model_class::DEFAULT_OBJECT_TYPES)) {
				return $model_class::DEFAULT_OBJECT_TYPES;
			}
		}

		if (empty($this->active_taxonomy_models[$blog_id][$taxonomy])) {
			return [];
		}

		return $this->active_taxonomy_models[$blog_id][$taxonomy];
	}

	/**
	 * Check if a model is a WP core post type model.
	 *
	 * @access public
	 *
	 * @param string $model The model slug.
	 *
	 * @return boolean
	 */
	public function isCorePostTypeModel($model)
	{
		return in_array($model, $this->core_post_type_models, true);
	}

	/**
	 * Check if a cpt model is enabled from the plugin settings.
	 *
	 * @access public
	 *
	 * @param string $model The model slug.
	 *
	 * @return boolean
	 */
	public function isEnabledCPTModel($model)
	{
		// Core post type models cannot be disabled.
		if ($this->isCorePostTypeModel($model)) {
			return true;
		}

		$blog_id = get_current_blog_id();

		return empty($this->disabled_cpts_models[$blog_id])
			|| !in_array($model, $this->disabled_cpts_models[$blog_id], true);
	}

	/**
	 * Check if a taxonomy model is enabled for a cpt.
	 *
	 * @access public
	 *
	 * @param string $cpt CPT model slug.
	 * @param string $taxonomy Taxonomy model slug.
	 *
	 * @return bool
	 */
	public function isEnabledCPTTaxonomyModel($cpt, $taxonomy)
	{
		return in_array($cpt, $this->getTaxonomyObjectTypes($taxonomy), true);
	}

	/**
	 * Load the defined cpt models.
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function loadCPTModels()
	{
		// Load all the models and initiate the objects.
		foreach (glob(CF_CPTS_DIR . 'classes/models/cpts/class-*.php') as $file) {
			// Double check the file name and skip the element if it's invalid.
			if (!preg_match('/class-(.*).php/', $file, $model)) {
				continue;
			}

			// Get the post type.
			$post_type = $model[1];

			// Load the model.
			require_once $file;

			// Set up the model class name and model.
			$model_class = self::getModelClassName('CPTs', $post_type);
			$model = $model_class::MODEL;

			// Add the post type to the cpt models list if it doesn't already exist.
			if (!in_array($model, $this->getCombinedPostTypeModels(), true)) {
				$this->cpt_models[] = $model;
			}

			// Initialize the model only if it's enabled.
			if ($this->isEnabledCPTModel($model)) {
				new $model_class();
			}
		}
	}

	/**
	 * Load the defined taxonomy models.
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function loadTaxonomyModels()
	{
		// Load all the models and initiate the objects.
		foreach (glob(CF_CPTS_DIR . 'classes/models/taxonomies/class-*.php') as $file) {
			// Double check the file name and skip the element if it's invalid.
			if (!preg_match('/class-(.*).php/', $file, $model)) {
				continue;
			}

			// Get the taxonomy name.
			$taxonomy = $model[1];

			// Load the model.
			require_once $file;

			// Set up the model class name and model.
			$model_class = self::getModelClassName('Taxonomies', $taxonomy);
			$model = $model_class::MODEL;

			// Add the taxonomy name to the taxonomy models list.
			$this->taxonomy_models[] = $model;

			// Do not initialize the model if it has no post types assigned.
			if (!$this->getTaxonomyObjectTypes($model)) {
				continue;
			}

			// Initialize the model.
			new $model_class();
		}
	}

	/**
	 * Method to change the source post ids from the meta data to the target post ids.
	 *
	 * @access public
	 *
	 * @param array $meta Meta data.
	 * @param \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context Context object.
	 *
	 * @return array
	 */
	public function multilingualPressPostMetaData($meta, $context)
	{
		if (!$meta || !is_array($meta)) {
			return $meta;
		}

		// These types of fields need to be processed and the post ids need to be converted to those of the remote site.
		$post_fields = [
			'file',
			'gallery',
			'image',
			'post_object',
			'relationship',
		];

		foreach ($meta as $key => $values) {
			if (!$values || !is_array($values)) {
				continue;
			}

			// Get field data name.
			$field_name = ltrim($key, '_');

			// Field data is not set.
			if (!isset($meta[$field_name])) {
				continue;
			}

			foreach ($values as $i => $value) {
				// Check if this is a field name definition and that its corresponding data is set.
				if (!is_string($value) || strpos($value, 'field_') === false || !isset($meta[$field_name][$i])) {
					continue;
				}

				// Get field definition.
				$field = get_field_object($value);

				// Check if the field type is one that needs processing.
				if (empty($field['type']) || !in_array($field['type'], $post_fields, true)) {
					continue;
				}

				// Data consist of an array of IDs.
				if (is_array($meta[$field_name][$i])) {
					foreach ($meta[$field_name][$i] as $j => $id) {
						try {
							// Function returns a pair of source post id, remote post id values.
							$translation_ids = \Inpsyde\MultilingualPress\translationIds(
								$id,
								'post',
								$context->sourceSiteId()
							);

							if ($translation_ids && is_array($translation_ids)) {
								// Replace original post id with remote post id.
								$meta[$field_name][$i][$j] = end($translation_ids);
							} else {
								unset($meta[$field_name][$i][$j]);
							}
						} catch (\Exception $e) {
							unset($meta[$field_name][$i][$j]);
						}
					}
				} else {
					// Data consists of a single ID.
					try {
						// Function returns a pair of source post id, remote post id values.
						$translation_ids = \Inpsyde\MultilingualPress\translationIds(
							$meta[$field_name][$i],
							'post',
							$context->sourceSiteId()
						);

						if ($translation_ids && is_array($translation_ids)) {
							// Replace original post id with remote post id.
							$meta[$field_name][$i] = end($translation_ids);
						} else {
							unset($meta[$field_name][$i]);
						}
					} catch (\Exception $e) {
						unset($meta[$field_name][$i]);
					}
				}
			}
		}

		return $meta;
	}

	/**
	 * Method to push ACF custom fields to be synced by MultilingualPress.
	 *
	 * @access public
	 *
	 * @param array $keys The keys of the metadata that will be synced.
	 * @param \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context Context object.
	 * @param \Inpsyde\MultilingualPress\Framework\Http\PhpServerRequest $request Request object.
	 *
	 * @return array
	 */
	public function multilingualPressSyncPostMetaKeys($keys, $context, $request)
	{
		if (!$context->sourceSiteId() || !$context->sourcePostId()) {
			return $keys;
		}

		// Change to source site.
		if (get_current_blog_id() !== $context->sourceSiteId()) {
			switch_to_blog($context->sourceSiteId());
			$changed_blog = true;
		}

		$meta = get_post_meta($context->sourcePostId(), false, true);

		// Restore current blog.
		if (!empty($changed_blog)) {
			restore_current_blog();
		}

		if ($meta && is_array($meta)) {
			foreach ($meta as $key => $values) {
				if (!$values || !is_array($values)) {
					continue;
				}

				foreach ($values as $value) {
					// Check if an ACF field record.
					if (is_string($value) && strpos($value, 'field_') !== false) {
						$keys[] = $key; // Get key for field name definition. I.e.: _foo => field_5ece7a07f6e9d.
						$keys[] = ltrim($key, '_'); // Get key for field data. I.e.: foo => Foo text.

						break;
					}
				}
			}
		}

		return $keys;
	}

	/**
	 * Add the plugin menu item.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function registerSettingsPage()
	{
		add_submenu_page(
			'options-general.php',
			__('CF CPTs Settings', 'cf-cpts'),
			'<div class="dashicons dashicons-index-card"></div> ' . __('CF CPTs Settings', 'cf-cpts'),
			'manage_options',
			'cf-cpts-page',
			[$this, 'renderSettingsPage']
		);
	}

	/**
	 * Insert a menu entry to Reusable Blocks under Appearance.
	 */
	public function insertReusableBlocksMenu()
	{
		global $submenu;
		$submenu['themes.php'][] = [
			esc_html__('Reusable Blocks', 'cf-cpts'),
			'edit_posts',
			admin_url('edit.php?post_type=wp_block')
		];
	}

	/**
	 * Highlight admin menu entry for reusable blocks.
	 * @param string $parentFile Menu parent file.
	 * @return string Updated parent file.
	 */
	public function reusableBlocksMenuParentFile(string $parentFile): string
	{
		global $submenu_file;

		$relativeUrl = 'edit.php?post_type=wp_block';

		if ($parentFile != $relativeUrl) {
			return $parentFile;
		}

		$postType = filter_input(INPUT_GET, 'post_type');
		if ($postType === 'wp_block') {
			$parentFile = 'themes.php';

			$adminUrl = trailingslashit(get_admin_url());
			$submenu_file = sprintf('%s%s', $adminUrl, $relativeUrl);
		}
		return $parentFile;
	}

	/**
	 * Exposes the plugin settings page.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function renderSettingsPage()
	{
		// phpcs:disable Generic.Files.LineLength.TooLong
		$post_type_models = $this->getCombinedPostTypeModels();

		?>
		<div class="cf-cpts-wrap">
			<h1>
				<div class="dashicons dashicons-index-card"></div>
				<?php esc_html_e('CF CPTs Settings', 'cf-cpts'); ?>
			</h1>
			<form action="" method="post">
				<?php wp_nonce_field('cf_disabled_cpts_models_action', 'cf_disabled_cpts_models_field'); ?>
				<?php if (!empty($post_type_models)) : ?>
					<?php
					esc_html_e(
						'This is the list of custom post types defined by the CF CPTs plugin. The models that are checked below are the models enabled for this site. If you want to disable any of the models listed here, you can un-check these, then click the update button, to save your options.',
						'cf-cpts'
					);
					?>
					<br>
					<?php foreach ($post_type_models as $post_type) : ?>
						<p>
							<label>
								<input type="checkbox"
									name="active_cpt_models[<?php echo esc_attr($post_type); ?>]"
									id="active_cpt_models_<?php echo esc_attr($post_type); ?>"
									<?php checked($this->isEnabledCPTModel($post_type), true); ?>
									<?php echo $this->isCorePostTypeModel($post_type) ? 'disabled' : ''; ?>
								>
								<?php echo esc_html($post_type); ?>
							</label>
							<?php if (!empty($this->taxonomy_models)) : ?>
								<div style="padding-left: 10px;">
									<?php foreach ($this->taxonomy_models as $taxonomy) : ?>
										<label>
											<input type="checkbox"
											   name="active_taxonomy_models[<?php echo esc_attr($taxonomy); ?>][<?php echo esc_attr($post_type); ?>]"
											   id="active_taxonomy_models_<?php echo esc_attr($post_type . '_' . $taxonomy); ?>"
												<?php
												checked(
													$this->isEnabledCPTTaxonomyModel($post_type, $taxonomy),
													true
												);
												?>
											>
											<?php echo esc_html($taxonomy); ?>
										</label>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</p>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php submit_button(esc_html__('Update Models', 'cf-cpts'), 'button-primary'); ?>
			</form>
			<hr>
		</div>
		<?php
		// phpcs:enable Generic.Files.LineLength.TooLong
	}

	/**
	 * Update the CPTs settings.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function updateDisabledCPTsModels()
	{
		$blog_id = get_current_blog_id();

		$this->disabled_cpts_models[$blog_id] = [];

		$selected = filter_input(INPUT_POST, 'active_cpt_models', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

		if (empty($selected)) {
			// If no CPT model is selected, then all of them are disabled.
			$this->disabled_cpts_models[$blog_id] = $this->cpt_models;
		} else {
			// Set the CPT models that are not selected as disabled.
			$this->disabled_cpts_models[$blog_id] = array_diff($this->cpt_models, array_keys($selected));
		}

		// Core post type models cannot be disabled.
		$this->disabled_cpts_models[$blog_id] = array_diff(
			$this->disabled_cpts_models[$blog_id],
			$this->core_post_type_models
		);

		update_option('cf_disabled_cpts_models', $this->disabled_cpts_models);
	}

	/**
	 * Save the plugin settings (disable models from being loaded).
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function updateSettings()
	{
		$update_settings = filter_input(INPUT_POST, 'cf_disabled_cpts_models_field', FILTER_DEFAULT);
		if (empty($update_settings) || !wp_verify_nonce($update_settings, 'cf_disabled_cpts_models_action')) {
			return;
		}

		$this->updateDisabledCPTsModels();
		$this->updateTaxonomiesSettings();

		if (wp_safe_redirect(admin_url('options-general.php?page=cf-cpts-page'))) {
			die;
		}
	}

	/**
	 * Update the taxonomies settings.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function updateTaxonomiesSettings()
	{
		$blog_id = get_current_blog_id();

		$this->active_taxonomy_models[$blog_id] = [];

		$selected = filter_input(INPUT_POST, 'active_taxonomy_models', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

		if (!empty($selected)) {
			foreach ($selected as $taxonomy => $cpts) {
				if (is_array($cpts)) {
					$this->active_taxonomy_models[$blog_id][$taxonomy] = array_keys($cpts);
				}
			}
		}

		update_option('cf_taxonomies_models', $this->active_taxonomy_models);
	}

	/**
	 * Check if the current page should force loading the javascript file needed for Global Media.
	 * @param \WP_Screen $currentScreen Current screen.
	 * @return bool True if current page should load Global Media javascript.
	 */
	private function shouldForceAdminGlobalMediaInScreen(\WP_Screen $currentScreen): bool
	{
		if (empty($this->globalMediaAcfScreens)) {
			return false;
		}

		foreach ($this->globalMediaAcfScreens as $screenFields) {
			$cond = true;
			foreach ($screenFields as $field => $val) {
				if (property_exists($currentScreen, $field)) {
					$cond &= ($currentScreen->{$field} === $val);
				} else {
					$cond = false;
					break;
				}
			}
			if ($cond) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Force load Global Media javascript by forcing the
	 * 'base' property of teh current screen to 'post.
	 * @return void
	 */
	public function forceLoadGlobalMediaJs()
	{
		global $current_screen;
		$this->screenBaseChanged = false;
		if ($this->shouldForceAdminGlobalMediaInScreen($current_screen)) {
			$this->storedCurrentScreenBase = $current_screen->base;
			// Force Global Media to load needed js.
			$current_screen->base = 'post';
			$this->screenBaseChanged = true;
		}
	}

	/**
	 * Restore the original 'base' property of the current screen.
	 * @return void
	 */
	public function afterForceLoadGlobalMediaJs()
	{
		global $current_screen;
		if ($this->screenBaseChanged) {
			$this->screenBaseChanged = false;
			$current_screen->base = $this->storedCurrentScreenBase;
		}
	}
}
