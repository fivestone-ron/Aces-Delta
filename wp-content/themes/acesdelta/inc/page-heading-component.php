<?php
function page_heading_component()
{
	$is_blank_canvas = mhps_check_blank_canvas_page();
	ob_start(); ?>
	<section class="heading js-heading <?php echo esc_attr(return_header_section_class()); ?>">
		<?php echo return_image(); ?>
		<header id="masthead" class="site-header js-site-header <?php echo $is_blank_canvas ? 'no-hero' : ''; ?>">
			<div class="ctn-main">
				<div class="site-branding">
					<!-- <?php
					if (is_front_page() || is_home()) : ?>
						<h1>
							<?php the_custom_logo(); ?>
						</h1>
					<?php else :
							the_custom_logo();
					endif; ?> -->
					<h1>
						<a href="/"><span>AcesDelta</span></a>
					</h1>
				</div><!-- .site-branding -->

				<div class="header__nav">
					<button class="menu-toggle" aria-controls="menu" aria-expanded="false" name="menu button">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<nav id="site-navigation" class="main-navigation" role="navigation">
						<div class="nav nav-primary">
							<?php wp_nav_menu(array('theme_location' => 'primary')); ?>
						</div>
					</nav><!-- #site-navigation -->
					<div class="search">
						<span class="search__icon js-search__icon"></span>
						<div class="search__form js-search__form">
							<div class="ctn-main">
								<?php get_search_form() ?>
							</div>
						</div>
					</div>
				</div> <!-- End .header__nav -->
			</div> <!-- End .ctn-main -->
		</header><!-- #masthead -->

		<?php if (!$is_blank_canvas) : ?>
			<div class="heading__banner <?php echo esc_attr(return_foreground_color()); ?>">
				<div class="ctn-main <?php echo esc_attr(return_content_vertical_align()); ?>">
					<?php if (empty(get_field('title', get_the_ID())) || '' === get_field('title', get_the_ID())) : ?>
						<h1 class="<?php echo esc_attr(return_title_size()); ?>"><?php echo esc_html(return_title()); ?></h1>
					<?php else : ?>
						<div class="<?php echo esc_attr(return_title_size()); ?>">
							<?php echo wp_kses_post(return_title()); ?>
						</div>
					<?php endif; ?>
					<div class="text">
						<?php echo wp_kses_post(return_text()); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<?php return ob_get_clean();
}

function return_image()
{
	$image = get_field('background_image', get_the_ID());

	ob_start();

	if (!mhps_check_blank_canvas_page()) {
		$image = get_field('background_image', get_the_ID());
		?>
		<picture>
			<?php
			if (!empty($image) && !is_search()) :
				$file = get_attached_file($image['ID']);
				$info = pathinfo($file);
				$webp = $info['dirname'] . '/' . $info['filename'] . '.webp';
				
				if (file_exists($webp)) :
					?>
					<source srcset="<?php echo substr_replace($image['url'], 'webp', strrpos($image['url'], '.') + 1); ?>" type="image/webp">
					<?php
				endif;
				?>

				<source srcset="<?php echo $image['url']; ?>" type="image/<?php echo $info['extension']; ?>">
				<img src="<?php echo $image['url']; ?>" alt="/" srcset="<?php echo $image['url']; ?>">

				<?php
			else :
				?>
				<img src="<?php echo get_template_directory_uri() . '/assets/images/default-heading-banner.png'; ?>" alt="/">
				<?php
			endif;
			?>
		</picture>
		<?php
	}

	return ob_get_clean();
}

function return_title()
{

	$title = get_field('title', get_the_ID());
	if (is_404()) {
		return 'Error 404';
	}
	if (is_search()) {
		return 'Search';
	}
	if ($title != '') {
		return $title;
	}
	return get_the_title(get_the_ID());
}

function return_text()
{
	$blurb = get_field('blurb', get_the_ID());

	if ($blurb != '' && !is_search()) {
		return $blurb;
	}
	return '';
}

function return_content_vertical_align()
{
	$align = get_field('content_vertical_align', get_the_ID());

	switch ($align) {
		case 'center':
			return 'vertical-align-center';
			break;
		case 'start':
			return 'vertical-align-start';
			break;
		case 'end':
			return 'vertical-align-end';
			break;
		case 'space-around':
			return 'vertical-align-space-around';
			break;
		case 'space-between':
			return 'vertical-align-space-between';
			break;
		case 'space-evenly':
			return 'vertical-align-space-evenly';
			break;
		default:
			break;
	}

	return 'vertical-align-center';
}


function return_title_size()
{

    $size = get_field('title_font_size', get_the_ID());

    if (is_search() || is_404()) {
        $size = 'large';
    }

    return 'font-' . $size;
}

function return_foreground_color()
{
	$foreground_color = get_field('foreground_color', get_the_ID());
	if ($foreground_color === true) {
		return '-black-foreground';
	}
	return '';
}

function return_header_section_class()
{
	$classes = [];
	
	if (mhps_check_blank_canvas_page()) {
		$classes[] = 'no-hero';
	}

	return implode(' ', $classes);
}
