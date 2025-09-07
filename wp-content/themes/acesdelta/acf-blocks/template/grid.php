<?php

/**
 * Block Name: Brochure Form
 *
 * This is the template that displays the Brochure Form block.
 */

// create id attribute for specific styling
$id = 'grid-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="grid js-grid <?php echo $align_class; ?>">
    <div class="ctn-main">
        <?php if (have_rows('item')) : ?>
            <div class="grid-wrapper js-grid-wrapper">
                <?php while (have_rows('item')) :
                    the_row();

                    $layout = get_sub_field('layout');
                    $content_type = get_sub_field('content_type');
                    $post = get_sub_field('post');
                    $custom_post = get_sub_field('custom_post');
                    $video = get_sub_field('videos'); ?>

                    <div class="grid__item js-grid__item <?php echo $layout; ?> <?php echo $content_type; ?>">
                        <?php if ($content_type == "post") {
                            $img_size = 'grid_img_medium';
                            if ($layout == 'big_square') {
                                $img_size = 'grid_img_large';
                            } ?>
                            <div class="grid__thumbnail" style="background-image: url(<?php echo get_the_post_thumbnail_url($post->ID, $img_size); ?>);"></div>
                            <div class="grid__content">
                                <h2><?php echo $post->post_title; ?></h2>
                                <?php if ($layout != "square" && $layout != "big_square") { ?>
                                    <?php echo $post->post_content; ?>
                                <?php } ?>
                                <a href="<?php echo get_permalink($post->ID); ?>">Read More</a>
                            </div>
                        <?php } elseif ($content_type == "videos") { ?>
                            <div class="grid__thumbnail video__thumbnail js-video__thumbnail" style="background-image: url(<?php echo $video["video_thumbnail"]["url"]; ?>);"></div>
                            <div class="grid__content">
                                <h2><?php echo $video["video_title"]; ?></h2>
                                <a target="_blank" href="<?php echo $video["video_link"]; ?>">Read More</a>
                            </div>
                            <div class="video-modal js-video-modal">
                                <div class="video-modal__ctn">
                                    <div class="video-modal__close-btn js-video-modal__close-btn"></div>
                                    <div class="video-container">
                                        <iframe title="MHPS Video" src="https://player.vimeo.com/video/<?php echo $video["video_id"]; ?>?color=D10A00" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="grid__thumbnail" style="background-image: url(<?php echo $custom_post["post_thumbnail"]["url"]; ?>);"></div>
                            <div class="grid__content">
                                <h2><?php echo $custom_post["post_title"]; ?></h2>
                                <?php if ($custom_post["post_content"]) {
                                    if ($layout != "square" && $layout != "big_square") { ?>
                                        <?php echo $custom_post["post_content"];
                                    }
                                } ?>
                                <a target="_blank" href="<?php echo $custom_post["external_link"]; ?>">Read More</a>
                            </div>
                        <?php } ?>
                    </div>

                <?php endwhile; ?>
            </div> <!-- End .grid-wrapper.js-grid-wrapper -->
        <?php endif; ?>
    </div> <!-- End .ctn-main -->
</section> <!-- End .grid.js-grid -->

<?php
