<?php

/**
 * The admin settings page template.
 *
 * @package CrowdFavorite\SpeedDaemonCF
 */

?>

<div class="wrap">
	<h1></h1> <?php //added an empty h1 to display notices above the page title ?>
	<img height="50px" src="<?php echo esc_url(CF_SPEED_DAEMON_URL . '/icon.png'); ?>" />
	<h1><?php esc_html_e('Speed Daemon by Crowd Favorite', 'cf-speed-daemon'); ?></h1>
	<p class="description">
		<?php
		echo sprintf(
			/* Translators: %1$s - anchor opened; %2$s - anchor closed; */
			esc_html__('See the %1$shelp page%2$s for additional usage information.', 'cf-speed-daemon'),
			'<a href="' . esc_url(admin_url('admin.php?page=cf-speed-daemon-help')) . '">',
			'</a>'
		);
		?>
	</p>
	<h2><?php esc_html_e('Settings', 'cf-speed-daemon'); ?></h2>
	<form method="post" action="options.php">
		<table class="form-table" role="presentation">
			<?php
			settings_fields('cf_speed_daemon_intro');
			settings_fields('cf_speed_daemon');
			do_settings_sections('cf-speed-daemon');
			submit_button();
			?>
	</form>
</div>
