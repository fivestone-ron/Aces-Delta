<?php

/**
 * Block Name: Resource Center
 *
 * This is the template that displays the Resource Center block.
 */

// get image field (array)
$block_title = get_field('block_title');
$button_title = get_field('button_title');
$button_link = get_field('button_link');
$type_of_display = get_field('type_of_display');


// create id attribute for specific styling
$id = 'resource-center-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="resource-center <?php echo $align_class; ?>">
    <div class="ctn-main">
        <div class="resource-center__heading">
            <h2><?php echo $block_title; ?></h2>
            <a href="<?php echo get_the_permalink($button_link->ID); ?>"><?php echo $button_title; ?> <span class="icon-arrow -red"></span></a>
        </div>
        <ul>
        <?php foreach (return_list_of_resource_center_posts() as $post) {
            if ($type_of_display == 'custom') {
                if (get_field('video_link')) {
                    $link = get_field('video_link');
                } elseif (get_field('pdf')) {
                    $link = get_field('pdf');
                    $link = $link['url'];
                } elseif (get_field('link')) {
                    $link = $post['link']['url'];
                } else {
                    $link = '';
                }
                if (get_field('overide_title')) {
                    $resource_title = get_field('overide_title');
                } else {
                    $resource_title = $post['title'];
                }

                ?><li>
                    <img src="<?php echo $post['image']['sizes']['resource_center_img'] ?>" alt="Resource Center Thumbnail">
                    <a target="<?php echo $post['link']['target'] ?>" href="<?php echo $link; ?>"><?php echo $resource_title; ?> <span class="icon-arrow -black"></span></a>
                </li><?php
            } else {
                ?><li>
                <?php
                if (has_post_thumbnail($post->ID)) {
                    echo get_the_post_thumbnail($post->ID, 'resource_center_img');
                } else {
                    ?><img src="<?php echo get_template_directory_uri() . '/assets/images/default_featured_image.png'; ?>" alt="Resource Center Thumbnail"><?php
                }
                $categories = get_the_category($post->ID);
                if ($categories[0]->slug == 'videos') {
                    $link = (get_field('vimeo_video_link', $post->ID) ? get_field('vimeo_video_link', $post->ID) : ''); ?>

                    <div class="video-modal js-video-modal">
                        <div class="video-modal__ctn">
                            <div class="video-modal__close-btn js-video-modal__close-btn"></div>
                            <div class="video-container">
                                <iframe title="MHPS Video" src="<?php echo $link?>" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>

                <?php } elseif ($categories[0]->slug == 'articles') {
                    $link = get_the_permalink($post->ID);
                } else {
                    $link = (get_field('pdf', $post->ID) ? get_field('pdf', $post->ID) : '');
                    $link = $link['url'];
                }

                
                $resource_title = (get_field('overide_title', $post->ID) ? get_field('overide_title', $post->ID) : $post->post_title );
                

                if ($categories[0]->slug == 'videos') { ?>
                    <span class="video-link js-video-link"><?php echo $resource_title; ?> <span class="icon-arrow -black"></span></span>
                <?php } else { ?>
                    <a <?php echo ($categories[0]->slug == 'infographics' || $categories[0]->slug == 'whitepapers') ? "target='_blank'" : ''; ?> href="<?php echo $link?>"><?php echo $resource_title; ?> <span class="icon-arrow -black"></span></a>
                <?php } ?>
                </li><?php
            }
        }?>
        </ul>
    </div>
</section>
<?php
