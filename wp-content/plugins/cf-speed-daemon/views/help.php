<?php

/**
 * The admin help page template.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

?>

<div class="wrap">
	<h1></h1> <?php //added an empty h1 to display notices above the page title ?>
	<img height="50px" src="<?php echo esc_url(CF_SPEED_DAEMON_URL . '/icon.png'); ?>" />
	<h1><?php esc_html_e('Speed Daemon by Crowd Favorite', 'cf-speed-daemon'); ?></h1>
	<p class="description"><?php esc_html_e('Detailed usage information', 'cf-speed-daemon'); ?></p>
	<h2><?php esc_html_e('Help', 'cf-speed-daemon'); ?></h2>
	<h2><?php esc_html_e('How does Speed Daemon work?', 'cf-speed-daemon'); ?></h2>
	<p>
		<?php
		esc_html_e(
			'Speed Daemon by Crowd Favorite optimizes your website
			performance by removing unnecessary CSS code on a per-page basis.',
			'cf-speed-daemon'
		);
		?>
	</p>
	<p>
		<?php
		esc_html_e(
			'This is achieved by sending the contents of a page
			to a dedicated Speed Daemon server, thus ensuring your server\'s performance.
			The server analyzes your page\'s HTML and CSS code in order to generate a page-specific optimized CSS file.
			This file is in turn sent back to your website,
			where it will be loaded in place of the identified page stylesheets.
			',
			'cf-speed-daemon'
		);
		?>
	</p>
	<p><strong>
		<?php esc_html_e('Speed Daemon by CrowdFavorite will never save your page content!', 'cf-speed-daemon'); ?>
	</strong></p>

	<h2><?php esc_html_e('Can I selectively exclude pages from being optimized?', 'cf-speed-daemon'); ?></h2>
	<p>
		<?php
		esc_html_e(
			'In the edit view of every page there is a Speed Daemon settings
			area where you can choose to selectively exclude that specific page.',
			'cf-speed-daemon'
		);
		?>
	</p>

	<h2><?php esc_html_e('Can I exclude specific CSS selectors?', 'cf-speed-daemon'); ?></h2>
	<p>
		<strong>
			<?php
			esc_html_e(
				'Yes! Several alternatives are available in terms of exclusion rules:',
				'cf-speed-daemon'
			);
			?>
		</strong>
	</p>
	<ul>
		<li>
			<strong><?php esc_html_e('Using the settings page', 'cf-speed-daemon'); ?></strong>
			<p class="description">
				<?php
				esc_html_e(
					'Selector list, pattern list and recursive pattern list options are detailed on the settings page.',
					'cf-speed-daemon'
				);
				?>
			</p>
		</li>
		<li>
			<strong><?php esc_html_e('Using CSS comments', 'cf-speed-daemon'); ?></strong>
			<p class="description">
				<?php
				esc_html_e(
					'Add an ignore line before any style declaration
					and Speed Daemon will skip it. The format of this ignore line should be ',
					'cf-speed-daemon'
				);
				?>
				<code>
					/* purgecss ignore */
				</code>
			</p>
		</li>
	</ul>

	<h2><?php esc_html_e('Is Speed Daemon compatible with Gutenberg?', 'cf-speed-daemon'); ?></h2>
	<p>
		<?php
		esc_html_e(
			'Yes it does! Whether you use the new WordPress Block Editor
			or the Classic Editor interface, Speed Daemon just works.',
			'cf-speed-daemon'
		);
		?>
	</p>

	<h2>
		<?php
		esc_html_e(
			'What about page builders like Beaver Builder or Elementor? Are those also compatible?',
			'cf-speed-daemon'
		);
		?>
	</h2>
	<p>
		<?php
		esc_html_e(
			'Yes. Speed Daemon has built-in support for both Beaver Builder and Elementor.
			However, since page builders are front-end visual tools, a custom integration
			is required to ensure they will function properly. If you have a preferred
			page builder that you would like to see supported by Speed Daemon, please reach out to us!',
			'cf-speed-daemon'
		);
		?>
	</p>

	<h2><?php esc_html_e('Can I use a CDN service, like Cloudflare?', 'cf-speed-daemon'); ?></h2>
	<p>
		<?php
		esc_html_e(
			'Yes, but you are likely going to need to add a custom rule
			to ensure your optimized CSS folder is included in the CDN rules.
			If your CDN also minifies CSS, you may need to adjust or disable
			this setting as minification is already handled by Speed Daemon.',
			'cf-speed-daemon'
		);
		?>
	</p>

	<h2>
		<?php
		esc_html_e(
			'Can I use Speed Daemon with caching plugins like Autoptimize or WP Rocket?',
			'cf-speed-daemon'
		);
		?>
	</h2>
	<p>
		<?php
		esc_html_e(
			'This depends on your setup. In general, if you use
			Autoptimize without its CSS optimization enabled, it should work fine.
			Due to how these tools interact with your website, you may experience
			issues when using multiple plugins to process CSS files.
			If you would like to keep using Autoptimize or other optimization plugins,
			we recommend disabling their CSS functionality in favor of Speed Daemon.',
			'cf-speed-daemon'
		);
		?>
	</p>

	<h2>
		<?php
		esc_html_e(
			'How does Speed Daemon compare with similar tools, like criticalcss.com?',
			'cf-speed-daemon'
		);
		?>
	</h2>
	<p>
		<?php
		esc_html_e(
			'A service like criticalcss.com will not reduce the overall CSS,
			it rather identifies styles necessary for the first part of the page,
			copies them to an inline style tag, then moves the rest of the CSS down
			the page so it does not slow down the initial page load.',
			'cf-speed-daemon'
		);
		?>
	</p>
	<p>
		<?php
		esc_html_e(
			'In contrast, Speed Demon reduces the overall CSS for a page
			by removing all the linked stylesheet files and replacing them
			with a single, highly optimized, CSS file. It concatenates styles
			in the order of appearance, preserving the original cascade.
			The final optimized CSS is then minified, further reducing the overall size.',
			'cf-speed-daemon'
		);
		?>
	</p>

	<h2>
		<?php
		esc_html_e(
			'Does Speed Daemon extract critical CSS separate from the rest of the page?',
			'cf-speed-daemon'
		);
		?>
	</h2>
	<p>
		<?php
		esc_html_e(
			'Speed Daemon will optimize the overall amount of CSS by removing styles
			that are not required for an individual page. However,
			it will not extract a page\'s critical CSS. Rest assured,
			critical CSS handling is on our roadmap and we are working
			hard to offer this functionality in an upcoming release.',
			'cf-speed-daemon'
		);
		?>
	</p>

</div>
