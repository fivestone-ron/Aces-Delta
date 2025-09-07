<?php
/*
Template Name: Tomoni
*/

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post(); ?>

  <div class="home-main-feature">
    <video muted autoplay loop id="bkgd-vid" class="bkgd-vid" oncanplay="this.muted=true">
      <source src="<?php the_field('header_video_url'); ?>" type="video/mp4">
      <source src="<?php the_field('header_video_url'); ?>" type="video/webm">
      <source src="<?php the_field('header_video_url'); ?>" type="video/ogg">
    </video>


            <?php $header_logo = get_field('header_logo');?>
      <img src="<?php echo $header_logo['url']; ?>" alt="<?php echo $header_logo['alt']; ?>">
  </div>

  <div class="container">
    <div class="intro">
            <?php the_field('intro_copy'); ?>
            <?php if (get_field('copy_section_image')) :
                $csi = get_field('copy_section_image'); ?>
        <img src="<?php echo $csi['url']; ?>" alt="<?php echo $csi['alt']; ?>">
            <?php endif; ?>
            <?php the_field('copy'); ?>
    </div>
    <div class="grid">
            <?php
            if (have_rows('page_ctas')) :
                while (have_rows('page_ctas')) :
                        the_row();
                      $pci = get_sub_field('image'); ?>
        <div>
          <img src="<?php echo $pci['url']; ?>" alt="<?php echo $pci['alt']; ?>" />
          <h3><?php the_sub_field('title'); ?></h3>
          <p><?php the_sub_field('short_description'); ?></p>
          <a class="red-link" href="<?php the_sub_field('page_link'); ?>"><?php the_sub_field('link_text'); ?> <span class="icon-arrow -red"></span></a>
        </div>
                <?php endwhile;
            endif; ?>
    </div>

        <?php get_template_part('template-parts/tp', 'featured'); ?>
  </div>

        <?php get_template_part('template-parts/content', 'page'); ?>

        <?php
    endwhile;
endif;

get_footer(); ?>