<?php

/**
 * Block Name: Resource Center
 *
 * This is the template that displays the Resource Center block.
 */

// create id attribute for specific styling
$id = 'resource-center-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="resource-center-grid grid js-grid <?php echo $align_class; ?>">
    <div class="ctn-main">
        <div class="grid-wrapper js-grid-wrapper">
            <?php
            $count      = 0;
            $layout     = '';
            $allPosts   = return_list_of_resource_center_posts_for_grid();
            $chunkPosts = array_chunk($allPosts, 6);
            if (!empty($chunkPosts)) {
                foreach ($chunkPosts as $subArray) {
                    foreach ($subArray as $index => $post) {
                        $categories = get_the_category($post->ID);
                        switch ($index) {
                            case 0:
                                $layout = "big_square";
                                break;
                            case 2:
                                $layout = "vertical_rectangle";
                                break;
                            case 3:
                            case 5:
                                $layout = "horizontal_rectangle";
                                break;
                            default:
                                $layout = "square";
                                break;
                        }
                        $thumbnail = '';
                        $alt       = '';
                        if (has_post_thumbnail($post->ID)) {
                            $img_size = 'grid_img_medium';
                            if ($layout == 'big_square') {
                                $img_size = 'grid_img_large';
                            }
                            $thumbnail = get_the_post_thumbnail_url($post->ID, $img_size);
                            $alt       = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
                        } else {
                            $thumbnail = get_template_directory_uri() . '/assets/images/default_featured_image.png';
                            $alt       = "Men in a Computer Room";
                        }
                        $resource_title = (get_field('overide_title', $post->ID) ? get_field('overide_title', $post->ID) : $post->post_title);
                        ?>
                        <div class="grid__item js-grid__item <?php echo $layout . " " . $categories[0]->slug; ?>">
                            <?php if ($categories[0]->slug == 'videos') { ?>
                                <div class="grid__thumbnail video__thumbnail js-video__thumbnail" style="background-image: url(<?php echo $thumbnail; ?>);"></div>

                                <div class="grid__content">
                                    <h2><?php echo $resource_title; ?></h2>
                                </div>
                                <div class="video-modal js-video-modal">
                                    <div class="video-modal__ctn">
                                        <div class="video-modal__close-btn js-video-modal__close-btn"></div>
                                        <div class="video-container">
                                            <?php $link = (get_field('vimeo_video_link', $post->ID) ? get_field('vimeo_video_link', $post->ID) : ''); ?>
                                            <iframe title="MHPS Video" src="<?php echo $link ?>" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>

                            <?php } else { ?>
                                <div class="grid__thumbnail" style="background-image: url(<?php echo $thumbnail; ?>);"></div>
                                <div class="grid__content">
                                    <h2><?php echo $post->post_title; ?></h2>
                                    <?php if ($layout != "square" && $layout != "big_square") { ?>
                                        <?php
                                        $text           = strip_shortcodes($post->post_content);
                                        $text           = apply_filters('the_content', $text);
                                        $text           = str_replace(']]>', ']]&gt;', $text);
                                        $excerpt_length = apply_filters('excerpt_length', 19);
                                        $excerpt_more   = apply_filters('excerpt_more', ' ' . '&hellip;');
                                        $text           = wp_trim_words($text, $excerpt_length, $excerpt_more);
                                        ?>
                                        <p><?php echo $text; ?></p>
                                    <?php } ?>
                                    <?php if ($categories[0]->slug == 'articles') { ?>
                                        <a href="<?php echo get_the_permalink($post->ID); ?>">Read More</a>
                                    <?php } else {
                                        $link = (get_field('pdf', $post->ID) ? get_field('pdf', $post->ID) : '');
                                        $link = $link['url']; ?>
                                        <a target="_blank" href="<?php echo $link; ?>">Read More</a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div> <!-- End .grid__item.js-grid__item -->
            <?php
                    }
                }
            }
            ?>
        </div> <!-- End .grid-wrapper.js-grid-wrapper -->
    </div>
</section>
<?php
