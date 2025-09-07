<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package New
 */

$categories = get_the_category(get_the_ID());
if (!empty($categories) && $categories[0]->slug === 'videos') {
    $link = get_field('vimeo_video_link', get_the_ID());
    wp_redirect($link);
    exit;
} elseif (!empty($categories) && $categories[0]->slug !== 'articles' && $categories[0]->slug !== 'videos') {
    $link = get_field('pdf', get_the_ID());
    $link = $link['url'];
    wp_redirect($link);
    exit;
}

get_header(); ?>

    <div id="primary">
        <main id="main" class="site-main">
            <div class="ctn-main">
                <?php
                while (have_posts()) :
                    the_post();

                    get_template_part('template-parts/content', get_post_type());

                    // the_post_navigation();
                endwhile; // End of the loop.
                ?>
            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
