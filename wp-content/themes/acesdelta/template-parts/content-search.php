<?php

/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package New
 */

$thumbnail = "";
$image = get_field('background_image', get_the_ID());
if (!empty($image)) {
    $thumbnail = $image['url'];
} else {
    $thumbnail = get_template_directory_uri() . '/assets/images/default-heading-banner-440x248.png';
}

$excerpt = "";
$blurb = get_field('blurb', get_the_ID());
if ($blurb != '') {
    $excerpt = $blurb;
} else {
    $excerpt = get_the_excerpt();
}


if (get_post_type(get_the_ID()) == 'post') {
    $categories = get_the_category($post->ID);
    $blank = '';
    if ($categories[0]->slug == 'videos') {
        $link = (get_field('vimeo_video_link', $post->ID) ? get_field('vimeo_video_link', $post->ID) : ''); ?>

    <?php } elseif ($categories[0]->slug == 'articles') {
        $link = get_the_permalink($post->ID);
    } else {
        $link = (get_field('pdf', $post->ID) ? get_field('pdf', $post->ID) : '');
        $link = $link['url'];
        $blank = '_blank';
    }
} else {
    $link = get_the_permalink();
    $blank = '_blank';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="search__post">
        <?php
        if (get_post_type(get_the_ID()) == 'post' && $categories[0]->slug == 'videos') {
            $link = (get_field('vimeo_video_link', get_the_ID()) ? get_field('vimeo_video_link', get_the_ID()) : ''); ?>
            <div class="video-modal js-video-modal">
                <div class="video-modal__ctn">
                    <div class="video-modal__close-btn js-video-modal__close-btn"></div>
                    <div class="video-container">
                        <iframe title="AcesDelta Video" src="<?php echo $link?>" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
                <header class="entry-header">
                    <?php the_title(sprintf('<h2 class="entry-title"><span class="video-link js-video-link">', esc_url($link)), '</span></h2>'); ?>
                </header><!-- .entry-header -->
                <?php
        } else {
            ?>
            <header class="entry-header">
                <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" target="' . $blank . '" rel="bookmark">', esc_url($link)), '</a></h2>'); ?>
            </header><!-- .entry-header -->
            <?php
        }
        ?>
        <div class="post__thumbnail">
            <?php if (has_post_thumbnail()) { ?>
                <?php the_post_thumbnail('resource_center_img'); ?>
            <?php } else {
                ?>
                <img src="<?php echo $thumbnail; ?>" alt="Post thumbnail">
            <?php } ?>
        </div>

        <div class="entry-summary">
            <p><?php echo $excerpt; ?></p>
        </div><!-- .entry-summary -->
    </div>

</article><!-- #post-<?php the_ID(); ?> -->